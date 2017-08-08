#!/bin/bash -xe
# install virtual env for python
sudo pip install virtualenv;
# install node js 5.x
# https://nodejs.org/en/download/package-manager/#debian-and-ubuntu-based-linux-distributions
curl -sL https://deb.nodesource.com/setup_5.x | sudo -E bash -;
sudo apt-get install -y nodejs;
sudo npm cache clean;
#install webpack and bower at global level
echo “installing webpack globally …”;
npm install -g webpack;
echo “installing bower globally …”;
npm install -g bower;
# update php.ini settings
upload_max_filesize=240M
post_max_size=240M
max_execution_time=300
max_input_time=223
display_errors=On
error_reporting=E_ALL
memory_limit=512M

for key in memory_limit upload_max_filesize post_max_size max_execution_time max_input_time display_errors error_reporting
do
 sed -i "s/^\($key\).*/\1 $(eval echo \=\${$key})/" /etc/php/5.6/fpm/php.ini
done

sudo service php5.6-fpm restart;

su vagrant;
# install local nodejs modules on VM
mkdir -p /home/vagrant/node_modules;
chown vagrant:www-data -R /home/vagrant/node_modules;
ln -sf /home/vagrant/node_modules /var/www/local.openstack.org/node_modules;

# install local bower modules on VM
mkdir -p /home/vagrant/bower_modules;
chown vagrant:www-data -R /home/vagrant/bower_modules;
ln -sf /home/vagrant/bower_modules /var/www/local.openstack.org/themes/openstack/bower_assets;

cd /var/www/local.openstack.org;
#composer installation from https://getcomposer.org/download/
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
# create local folder for ss cache
mkdir -p /var/www/local.openstack.org/silverstripe-cache;
php composer.phar install --ignore-platform-reqs --prefer-dist;
sudo ./framework/sake installsake;


if [[ -d /var/www/local.openstack.org/survey_builder/code/model/extract_tags ]]; then
	echo "installing python virtual env for extract tags";
	cd /var/www/local.openstack.org/survey_builder/code/model/extract_tags && virtualenv env;
	source /var/www/local.openstack.org/survey_builder/code/model/extract_tags/env/bin/activate;
	/var/www/local.openstack.org/survey_builder/code/model/extract_tags/env/bin/python env/bin/pip install -r requirements.txt;
	chmod 770 /var/www/local.openstack.org/survey_builder/code/model/extract_tags/extract_tags_by_rake.sh
	chmod 770 /var/www/local.openstack.org/survey_builder/code/model/extract_tags/extract_tags_by_kmeans.sh
fi

