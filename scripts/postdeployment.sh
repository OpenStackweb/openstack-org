#!/bin/bash -xe
cd /var/www/local.openstack.org;
sudo curl -sS https://getcomposer.org/installer | php;
sudo php composer.phar install;