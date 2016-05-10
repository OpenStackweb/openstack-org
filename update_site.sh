#!/usr/bin/env bash
echo "installing npm dependencies ...";
if [ -f "package.json" ]; then
    sudo npm install;
fi
echo "installing bower dependencies ...";
if [ -f "bower.json" ]; then
    bower install --allow-root --config.interactive=false
fi
echo "installing webpack dependencies ...";
if [ -f "webpack.config.js" ]; then
    webpack;
fi
echo "installing composer dependencies ...";
php composer.phar update --prefer-dist
php composer.phar dump-autoload --optimize
sudo ./framework/sake installsake;
echo "updating DB ...";
sake dev/build;
echo "running DB migrations  ...";
sake dev/tasks/DBMigrateTask;