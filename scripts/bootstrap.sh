#!/bin/bash -xe
mkdir -p /etc/puppet/modules;
puppet module install --force --module_repository https://forge.puppet.com puppetlabs-apt;
puppet module install --force --module_repository https://forge.puppet.com puppetlabs-stdlib;
puppet module install --force --module_repository https://forge.puppet.com puppetlabs-mysql;
puppet module install --force --module_repository https://forge.puppet.com jfryman-nginx;
# Set up environment variables, adding the new tools to PATH.
sudo sh -c "cat > /etc/profile.d/composer.sh" <<'EOF'
export COMPOSER_HOME=/var/www/local.openstack.org
EOF
