#!/usr/bin/env bash
git submodule init
git submodule update
git submodule foreach git pull --rebase origin master
if [ -f "package.json" ]; then
    sudo npm install;
fi
if [ -f "bower.json" ]; then
    bower install --allow-root --config.interactive=false
fi
if [ -f "webpack.config.js" ]; then
    webpack;
fi
php composer.phar composer self-update;
php composer.phar update --prefer-dist
php composer.phar dump-autoload --optimize
sudo ./framework/sake installsake;
sake dev/build;
sake dev/tasks/DBMigrateTask;