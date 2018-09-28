#!/bin/bash -xe
echo “running update deployment …”;
cd /var/www/www.openstack.org;
./clear_ss_cache.sh;
php composer.phar update --ignore-platform-reqs --prefer-dist;
#run ss tasks
sake dev/build;
sake dev/tasks/DBMigrateTask;
sudo rm -Rf node_modules;
sudo npm install;
sudo npm run build-all;
chown vagrant:www-data -R /home/vagrant/node_modules;
chown vagrant:www-data -R /var/www/www.openstack.org/vendor;
sudo service nginx restart;
sudo service php5.6-fpm restart;
sudo php /var/www/www.openstack.org/framework/cli-script.php /UpdateFeedTask;
