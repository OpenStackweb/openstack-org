#!/bin/bash -xe

hostname local.openstack.org
echo "127.0.0.1 local.openstack.org" >> /etc/hosts
mkdir -p /etc/puppet/modules;
puppet module install puppetlabs-nodejs;
puppet module install jfryman-nginx;
puppet module install puppetlabs-mysql;
# Set up environment variables, adding the new tools to PATH.
sudo sh -c "cat > /etc/profile.d/composer.sh" <<'EOF'
export COMPOSER_HOME=/var/www/local.openstack.org
EOF
