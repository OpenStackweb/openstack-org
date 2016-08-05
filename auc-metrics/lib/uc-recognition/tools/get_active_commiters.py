#!/usr/bin/env python

# Copyright (C) 2013-2014 OpenStack Foundation
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
#
# See the License for the specific language governing permissions and
# limitations under the License.
#
# Soren Hansen wrote the original version of this script.
# James Blair hacked it up to include email addresses from gerrit.
# Jeremy Stanley overhauled it for gerrit 2.8 and our governance repo.
# Tom Fifield cut it to pieces as a quick hack for the UC recognition to be
# replaced with something nicer at the earliest possible time

import datetime
import json
import optparse
import os
import os.path
import re
import io

import paramiko

MAILTO_RE = re.compile('mailto:(.*)')
USERNAME_RE = re.compile('username:(.*)')

class Account(object):
    def __init__(self, num):
        self.num = num
        self.full_name = ''
        self.emails = []
        self.username = None


def get_account(accounts, num):
    a = accounts.get(num)
    if not a:
        a = Account(num)
        accounts[num] = a
    return a


def repo_stats(repo, output, begin, end, keyfile, user):
    username_accounts = {}
    atcs = []

    QUERY = "project:%s status:merged" % repo

    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    client.load_system_host_keys()
    client.connect(
        'review.openstack.org', port=29418,
        key_filename=os.path.expanduser(keyfile), username=user)
    stdin, stdout, stderr = client.exec_command(
        'gerrit query %s --all-approvals --format JSON' % QUERY)

    done = False
    last_sortkey = ''
    begin_time = datetime.datetime(
        int(begin[0:4]), int(begin[4:6]), int(begin[6:8]),
        int(begin[8:10]), int(begin[10:12]), int(begin[12:14]))
    end_time = datetime.datetime(
        int(end[0:4]), int(end[4:6]), int(end[6:8]),
        int(end[8:10]), int(end[10:12]), int(end[12:14]))

    count = 0
    earliest = datetime.datetime.now()
    while not done:
        for l in stdout:
            data = json.loads(l)
            if 'rowCount' in data:
                if data['rowCount'] < 500:
                    done = True
                continue
            count += 1
            if 'sortKey' in data.keys():
                last_sortkey = data['sortKey']
            if 'owner' not in data:
                continue
            if 'username' not in data['owner']:
                continue
            account = Account(None)
            account.username = data['owner']['username']
            account.emails = [data['owner']['email']]
            account.full_name = data['owner']['name']
            approved = False
            for ps in data['patchSets']:
                if 'approvals' not in ps:
                    continue
                for aprv in ps['approvals']:
                    if aprv['type'] != 'SUBM':
                        continue
                    ts = datetime.datetime.fromtimestamp(aprv['grantedOn'])
                    if ts < begin_time or ts > end_time:
                        continue
                    approved = True
                    if ts < earliest:
                        earliest = ts
            if approved and account not in atcs:
                atcs.append(account)
        if not done:
            stdin, stdout, stderr = client.exec_command(
                'gerrit query %s resume_sortkey:%s --all-approvals'
                ' --format JSON' % (QUERY, last_sortkey))

    print 'repo: %s' % repo
    print 'examined %s changes' % count
    print 'earliest timestamp: %s' % earliest
	
    if not os.path.exists(os.path.dirname(output)):
    	try:
        	os.makedirs(os.path.dirname(output))
    	except OSError as exc: # Guard against race condition
        	if exc.errno != errno.EEXIST:
        		raise
    
    output_file = io.open(output, 'w', encoding='UTF-8')
    for a in atcs:
        output_file.write(a.username + ","+ a.full_name + "," + a.emails[0] + "\n")
    output_file.close()


def main():
    now = ''.join(
        '%02d' % x for x in datetime.datetime.utcnow().utctimetuple()[:6])

    optparser = optparse.OptionParser()
    optparser.add_option(
        '-b', '--begin', help='begin date/time (e.g. 20131017000000)')
    optparser.add_option(
        '-e', '--end', default=now, help='end date/time (default is now)')
    optparser.add_option(
        '-k', '--keyfile', default='~/.ssh/id_rsa',
        help='SSH key (default is ~/.ssh/id_rsa)')
    optparser.add_option(
   	    '-p', '--path', default='.',
   	    help='Output path, e.g. /path/to/output')

    options, args = optparser.parse_args()
    user = args[0]

    projects = ['openstack/ops-tags-team',
                'openstack/osops-tools-monitoring',
                'openstack/osops-tools-generic',
                'openstack/osops-example-configs',
                'openstack/osops-tools-logging',
                'openstack/osops-tools-contrib']

    for repo in projects:
        output = '%s/%s.csv' % (options.path, repo.split('/')[-1])
        repo_stats(repo, output, options.begin, options.end,
                   options.keyfile, user)


if __name__ == "__main__":
    main()
