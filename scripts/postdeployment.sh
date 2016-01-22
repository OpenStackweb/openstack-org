#!/bin/bash -xe

curl -sL https://deb.nodesource.com/setup_5.x | sudo -E bash -;
sudo apt-get install -y nodejs;
npm cache clean;
npm install -g webpack;
npm install -g bower;

upload_max_filesize=240M
post_max_size=240M
max_execution_time=100
max_input_time=223
display_errors=On
error_reporting=E_ALL

for key in upload_max_filesize post_max_size max_execution_time max_input_time display_errors error_reporting
do
 sed -i "s/^\($key\).*/\1 $(eval echo \=\${$key})/" /etc/php5/fpm/php.ini
done

cd /var/www/local.openstack.org;
mkdir -p silverstripe-cache;
sudo npm install;
sudo bower install --allow-root --config.interactive=false;
sudo curl -sS https://getcomposer.org/installer | php;
sudo php composer.phar install;
sudo ./framework/sake installsake;
sake dev/build;
sake dev/tasks/DBMigrateTask;
sudo service nginx restart;
sudo service php5-fpm restart;
sudo php /var/www/local.openstack.org/framework/cli-script.php /UpdateFeedTask;
