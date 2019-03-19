#!/usr/bin/env bash

sudo npm install;
npm run build-all

echo "installing composer dependencies ...";
php composer.phar update --prefer-dist
php composer.phar dump-autoload --optimize
sudo ./framework/sake installsake;
echo "updating DB ...";
sake dev/build;
echo "running DB migrations  ...";
sake dev/tasks/DBMigrateTask;
./sass.sh;
echo "compiling *.po files...";
sudo sake dev/tasks/ZanataServerPOFilesDownloaderTask;
sudo sake dev/tasks/ZanataServerPOFilesDownloaderTask module=papers;
sudo sake dev/tasks/CompilePO2MOTask;
sudo sake dev/tasks/CompilePO2MOTask module=papers;
sudo sake dev/tasks/PaperParseTranslatorsPOFiles;