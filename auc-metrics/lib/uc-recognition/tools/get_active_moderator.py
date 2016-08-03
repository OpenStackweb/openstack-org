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

import datetime
import json
import requests


user_list = 'https://ask.openstack.org/en/api/v1/users/'

params = dict(
    sort='reputation',
    page=1
)


def get_user_data(karma_level):
    """
    Loop through the user list to find users that have greater karma than
    karma level.
    Returns a list of user data dicts.
    """
    page = 1
    session = requests.Session()
    response = session.get(user_list, params=params)
    user_data = json.loads(response.text)['users']
    while user_data[-1]['reputation'] >= karma_level:
        page = page + 1
        params.update({'page': page})
        print "Getting page: %d" % page
        response = session.get(user_list, params=params)
        user_data.extend(json.loads(response.text)['users'])

    # since pages are big chunks, we will have some users that are
    # having karma lower than karma_level in the last page. Remove them.
    while user_data[-1]['reputation'] < karma_level:
        user_data.pop()

    return user_data


def get_active_users(user_data, last_active_days=180):
    """
    Give a list of user dict objects, return the ones that
    were active within the number of days specificed by
    last_active days.
    Prints a list of usernames, reputations and IDs
    """

    now = datetime.datetime.now()
    active_threshold = now - datetime.timedelta(days=last_active_days)
    for user in user_data:
        last_seen_at = datetime.datetime.fromtimestamp(
                          int(user['last_seen_at']))
        if last_seen_at > active_threshold:
            print "{: <20} {: <20}".format(user['username'], str(user['id']))


def main():
    user_data = get_user_data(karma_level=200)
    get_active_users(user_data, last_active_days=180)


if __name__ == "__main__":
    main()
