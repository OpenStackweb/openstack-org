#!/bin/bash -xe
cd /var/www/local.openstack.org;
sudo php composer.phar update --ignore-platform-reqs;
#run ss tasks
sake dev/build;
sake dev/tasks/DBMigrateTask;
sudo npm install;
sudo bower install --allow-root --config.interactive=false;
chown vagrant:www-data -R /home/vagrant/node_modules;
chown vagrant:www-data -R /home/vagrant/bower_modules;
chown vagrant:www-data -R /var/www/local.openstack.org/vendor;
sudo service nginx restart;
sudo service php5-fpm restart;
sudo php /var/www/local.openstack.org/framework/cli-script.php /UpdateFeedTask;

