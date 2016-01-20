#!/bin/bash -xe

mkdir -p /etc/puppet/modules;
puppet module install puppetlabs-apt;
puppet module install jfryman-nginx;
puppet module install puppetlabs-mysql;
# Set up environment variables, adding the new tools to PATH.
sudo sh -c "cat > /etc/profile.d/composer.sh" <<'EOF'
export COMPOSER_HOME=/var/www/local.openstack.org
EOF
