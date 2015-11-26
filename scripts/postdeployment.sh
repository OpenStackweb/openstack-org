#!/bin/bash -xe
cd /var/www/local.openstack.org;
mkdir -p silverstripe-cache;
sudo curl -sS https://getcomposer.org/installer | php;
sudo php composer.phar install;
sudo ./framework/sake installsake;
sake dev/build;
sake dev/tasks/DBMigrateTask;
sudo service nginx restart;
sudo php /var/www/local.openstack.org/framework/cli-script.php /UpdateFeedTask;
