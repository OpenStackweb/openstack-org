#!/usr/bin/env bash

sudo npm install;
bower install --allow-root --config.interactive=false
npm run build-all

echo "installing composer dependencies ...";
php composer.phar update --prefer-dist
php composer.phar dump-autoload --optimize
sudo ./framework/sake installsake;
echo "updating DB ...";
sake dev/build;
echo "running DB migrations  ...";
sake dev/tasks/DBMigrateTask;