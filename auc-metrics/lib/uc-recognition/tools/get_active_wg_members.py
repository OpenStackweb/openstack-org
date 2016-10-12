#!/usr/bin/env python
#
# Copyright (c) 2016 OpenStack Foundation
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#    http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or
# implied.
# See the License for the specific language governing permissions and
# limitations under the License.

from datetime import datetime
from datetime import timedelta
import operator
import optparse
import os

meeting_mappings = {
'uc': 'user_committee',
'product_team': 'product_working_group',
'large_deployments_team_december_2015_meeting': 'large_deployment_team',
'large_deployments_team_february_2016_meeting': 'large_deployment_team',
'large_deployments_team_monthly_meeting': 'large_deployment_team',
'large_deployments_team_january_2016_meeting': 'large_deployment_team',
'large_deployments_team_october_2015_meeting': 'large_deployment_team'
}


def get_recent_meets(log_dir, last_active_days=180):
    """
    takes a directory heirachy that only contains meetbot
    txt summary files, determines the users active within
    the threshold. Returns a dictionary that has
    one entry per meeting category, containing information about
    who attended which meetings and how much they said.
    """
    meetings = {}
    now = datetime.now()
    active_threshold = now - timedelta(days=last_active_days)

    # get our list of meetings and timestamps
    for root, dirs, files in os.walk(log_dir):
        if len(files) > 0:
            for txt_summary in files:
                (meet_name, meet_date) = txt_summary.split('.', 1)
                meet_date = meet_date[0:-4]  # drop the .txt at the end
                if meet_name in meeting_mappings.keys():
                    meet_name = meeting_mappings[meet_name]
                meet_timestamp = datetime.strptime(meet_date, "%Y-%m-%d-%H.%M")
                if meet_timestamp > active_threshold:
                    if meet_name not in meetings.keys():
                        meetings[meet_name] = []
                    meet_file = root + "/" + txt_summary
                    meetings[meet_name].append(get_people_in_meeting(meet_file))

    return meetings


def get_people_in_meeting(meeting_txt):
    """
    takes a meetbot summary file that has a section with the following format
    and returns a dict with username<->lines said mapping

    People present (lines said)
    ---------------------------

    * username (117)
    * username2 (50)
    """
    meeting_people = []
    in_people = False
    txt_file = open(meeting_txt)
    for line in txt_file:
        if line == "People present (lines said)\n":
            in_people = True
        elif not in_people:
            next
        elif in_people and '*' not in line:
            next
        elif in_people and 'openstack' not in line:
            ircnic, linessaid = line[2:-2].split('(')
            ircnic = ircnic.strip(" _").lower()
            meeting_people.append((ircnic, linessaid))

    txt_file.close()
    return meeting_people


def get_meeting_aggregates(meeting_data):
    """
    Aggregates the attendance counts and lines said for users across
    a meeting category
    """
    meeting_aggregate = {}
    for meeting_name in meeting_data.keys():
        meeting_users = {}
        for meeting in meeting_data[meeting_name]:
            for user_tuple in meeting:
                if user_tuple[0] not in meeting_users.keys():
                    meeting_users[user_tuple[0]] = {'attendance_count': 1,
                                                    'lines_said': int(user_tuple[1])}
                else:
                    meeting_users[user_tuple[0]]["attendance_count"] += 1
                    meeting_users[user_tuple[0]]["lines_said"] += int(user_tuple[1])
        meeting_aggregate[meeting_name] = meeting_users
    return meeting_aggregate


def print_meet_stats(meeting_data):
    for meeting_name in meeting_data.keys():
        print "\n" + meeting_name + "\n=====================================\n"
        sorted_users = sorted(meeting_data[meeting_name].items(), reverse=True,
                              key=operator.itemgetter(1))
        for user in sorted_users:
            print "{: <20} {: <20} {: <20}".format(user[0],
                                                   user[1]["attendance_count"],
                                                   user[1]["lines_said"])


def print_eligible_usernames(meeting_data, num_meetings=1, lines_said=1, human=False):
    user_aggregate = {}
    for meeting_name in meeting_data.keys():
        for user_tuple in meeting_data[meeting_name].items():
            if user_tuple[0] not in user_aggregate.keys():
                user_aggregate[user_tuple[0]] = user_tuple[1]
            else:
                user_aggregate[user_tuple[0]]["lines_said"] += user_tuple[1]["lines_said"]
                user_aggregate[user_tuple[0]]["attendance_count"] += user_tuple[1]["attendance_count"]
    if human:
        print "\n OVERALL STATS \n=====================================\n"
    sorted_users = sorted(user_aggregate.items(), reverse=True,
                          key=operator.itemgetter(1))
    for user in sorted_users:
        if user[1]["attendance_count"] >= num_meetings or user[1]["lines_said"] >= lines_said:
            if human:
                print "{: <20} {: <20} {: <20}".format(user[0],
                                                       user[1]["attendance_count"],
                                                       user[1]["lines_said"])
            else:
                print "{},{},{}".format(user[0],
                                                       user[1]["attendance_count"],
                                                       user[1]["lines_said"])


def main():
    optparser = optparse.OptionParser()
    optparser.add_option(
        '--human', help='If set, output results in human-readable format',
        default=False, action="store_true")
    optparser.add_option(
        '-d', '--datadir', help='Where meeting data lives',
        default='./eavesdrop.openstack.org/meetings')
    optparser.add_option(
        '-t', '--days', help='Validity of attendance in days',
        type="int", default=183)
    optparser.add_option(
        '-n', '--nummeetings', help='Required number of meetings',
        type="int", default=2)
    optparser.add_option(
        '-l', '--linessaid', help='Required number of line said',
        type="int", default=10)
    options, args = optparser.parse_args()

    meeting_data = get_recent_meets(options.datadir, options.days)
    meeting_aggregate = get_meeting_aggregates(meeting_data)
    if options.human:
        print_meet_stats(meeting_aggregate)
    print_eligible_usernames(meeting_aggregate, options.nummeetings,
                             options.linessaid, options.human)

if __name__ == "__main__":
    main()