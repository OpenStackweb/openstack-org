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

mkdir -p /etc/puppet/modules;
puppet module install --force --module_repository https://forge.puppet.com puppetlabs-apt;
puppet module install --force --module_repository https://forge.puppet.com puppetlabs-stdlib;
puppet module install --force --module_repository https://forge.puppet.com --version 3.9.0 puppetlabs-mysql;
puppet module install --force --module_repository https://forge.puppet.com --version 0.3.0 jfryman-nginx;
puppet module install --force --module_repository https://forge.puppet.com --version 3.0.2 petems-swap_file;
# Set up environment variables, adding the new tools to PATH.
sudo sh -c "cat > /etc/profile.d/composer.sh" <<'EOF'
export COMPOSER_HOME=/var/www/local.openstack.org
EOF
