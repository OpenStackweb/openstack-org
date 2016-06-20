#!/bin/bash -xe

curl -sL https://deb.nodesource.com/setup_5.x | sudo -E bash -;
sudo apt-get install -y nodejs;
npm cache clean;
npm install -g webpack;
npm install -g bower;

upload_max_filesize=240M
post_max_size=240M
max_execution_time=300
max_input_time=223
display_errors=On
error_reporting=E_ALL

for key in upload_max_filesize post_max_size max_execution_time max_input_time display_errors error_reporting
do
 sed -i "s/^\($key\).*/\1 $(eval echo \=\${$key})/" /etc/php5/fpm/php.ini
done

cd /var/www/local.openstack.org;
mkdir -p silverstripe-cache;
sudo npm install --no-bin-links;
sudo bower install --allow-root --config.interactive=false;
#composer installation from https://getcomposer.org/download/
sudo php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');";
sudo php -r "if (hash_file('SHA384', 'composer-setup.php') === '070854512ef404f16bac87071a6db9fd9721da1684cd4589b1196c3faf71b9a2682e2311b36a5079825e155ac7ce150d') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;";
sudo php composer-setup.php;
sudo php -r "unlink('composer-setup.php');";
sudo php composer.phar install --ignore-platform-reqs;
sudo ./framework/sake installsake;
sake dev/build;
sake dev/tasks/DBMigrateTask;
sudo service nginx restart;
sudo service php5-fpm restart;
sudo php /var/www/local.openstack.org/framework/cli-script.php /UpdateFeedTask;
