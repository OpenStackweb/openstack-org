#!/usr/bin/env bash
sudo apt-get update;
sudo apt-get install -y nodejs;
sudo rm -R vendor;
sudo rm -R node_modules;
php -r "readfile('https://getcomposer.org/installer');" > composer-setup.php
php -r "if (hash('SHA384', file_get_contents('composer-setup.php')) === 'fd26ce67e3b237fffd5e5544b45b0d92c41a4afe3e3f778e942e43ce6be197b9cdc7c251dcde6e2a52297ea269370680') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); }"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo npm install -g n;
sudo n latest;
echo "installing webpack ...";
sudo npm install -g webpack;
echo "installing bower ...";
sudo npm install -g bower;
echo "installing composer dependencies ...";
php composer.phar install --prefer-dist
php composer.phar dump-autoload --optimize
mkdir -p assets
sudo chmod 775 assets;
mkdir -p silverstripe-cache
sudo chmod 775 silverstripe-cache;
\sudo npm install;
bower install --allow-root --config.interactive=false;
npm run build-all;
sudo ./framework/sake installsake;
sake dev/build;
sake dev/tasks/DBMigrateTask;
./sass.sh;
