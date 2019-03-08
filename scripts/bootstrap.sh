#!/bin/bash
# Copyright (c) 2017 OpenStack Foundation
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

GITHUB_OAUTH_TOKEN=$1

echo "GITHUB_OAUTH_TOKEN $GITHUB_OAUTH_TOKEN";

sudo apt-get update;
sudo apt-get --yes upgrade;
sudo apt-get --yes install puppet composer;
composer config -g github-oauth.github.com $GITHUB_OAUTH_TOKEN;
puppet --version;
mkdir -p /etc/puppet/modules;

puppet module install --force --module_repository https://forge.puppet.com --version 5.2.0 puppetlabs-stdlib;
puppet module install --force --module_repository https://forge.puppet.com --version 8.0.0 puppetlabs-mysql;
puppet module install --force --module_repository https://forge.puppet.com --version 0.16.0 puppet-nginx;
puppet module install --force --module_repository https://forge.puppet.com --version 4.0.0 petems-swap_file;
puppet module install --force --module_repository https://forge.puppet.com --version 6.3.0 puppetlabs-apt;
puppet module install --force --module_repository https://forge.puppet.com --version 7.0.0 puppet-nodejs;
# Set up environment variables, adding the new tools to PATH.
sudo sh -c "cat > /etc/profile.d/composer.sh" <<'EOF'
export COMPOSER_HOME=/var/www/www.openstack.org
EOF
