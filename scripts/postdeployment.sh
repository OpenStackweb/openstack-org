#!/bin/bash -xe
cd /var/www/local.openstack.org;
mkdir -p silverstripe-cache;
sudo curl -sS https://getcomposer.org/installer | php;
sudo php composer.phar install;
sudo ./framework/sake installsake;
sake dev/build;
