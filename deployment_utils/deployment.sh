#!/bin/bash
DEPLOYMENT_DIR=$1
REPO_BRANCH=$2
REPO_URL=$3
CONFIG_DIR=$4
BLOG_DIR=$5
BLOCK_STORAGE_DIR=$6
HOST_HEADER=$7
APACHE_DIR=$8
USE_COMPOSER=$9
REBUILD_DB=${10}
RELEASE_ID=${11}
REVISION=${12}

eval DEPLOYMENT_DIR=$DEPLOYMENT_DIR;
echo "deployment folder set to $DEPLOYMENT_DIR";
echo "branch $REPO_BRANCH";
echo "release id $RELEASE_ID";
echo "revision $REVISION";

#create deployment dir
mkdir -p $DEPLOYMENT_DIR/$RELEASE_ID;
#clone from git repo
git clone -b $REPO_BRANCH $REPO_URL $DEPLOYMENT_DIR/$RELEASE_ID;
cd $DEPLOYMENT_DIR/$RELEASE_ID && git reset --hard $REVISION;
php $DEPLOYMENT_DIR/$RELEASE_ID/deployment_utils/preprocess.php $DEPLOYMENT_DIR
#configuration files
echo "copying configuration files from $CONFIG_DIR ...";
ln -s $CONFIG_DIR/.htaccess $DEPLOYMENT_DIR/$RELEASE_ID/.htaccess;
cp $CONFIG_DIR/_ss_environment.php $DEPLOYMENT_DIR/$RELEASE_ID/_ss_environment.php;
#link for block storage (assets)
ln -s $BLOCK_STORAGE_DIR/assets $DEPLOYMENT_DIR/$RELEASE_ID/assets;
#link for os blog
ln -s $BLOG_DIR $DEPLOYMENT_DIR/$RELEASE_ID/blog;
echo "\$_FILE_TO_URL_MAPPING['$DEPLOYMENT_DIR/$RELEASE_ID'] = '$HOST_HEADER';" >>  $DEPLOYMENT_DIR/$RELEASE_ID/_ss_environment.php;
mkdir -p $DEPLOYMENT_DIR/$RELEASE_ID/silverstripe-cache;
#composer
if [[ $USE_COMPOSER -eq 1 ]]; then
  echo "invoking composer..."
  cd $DEPLOYMENT_DIR/$RELEASE_ID && curl -sS https://getcomposer.org/installer | php;
  cd $DEPLOYMENT_DIR/$RELEASE_ID && php composer.phar install;
  cd $DEPLOYMENT_DIR/$RELEASE_ID && php composer.phar dump-autoload --optimize;
fi
#permissions
find $DEPLOYMENT_DIR/$RELEASE_ID -type d -print0 | xargs -0 chmod 775;
find $DEPLOYMENT_DIR/$RELEASE_ID -type f -print0 | xargs -0 chmod 644;
chmod 640 $DEPLOYMENT_DIR/$RELEASE_ID/_ss_environment.php;
sudo chown -R beanstalk:www-data $DEPLOYMENT_DIR/$RELEASE_ID;
#db build
if [[ $REBUILD_DB -eq 1 ]]; then
  echo 'invoking dev/build?flush=all';
  php $DEPLOYMENT_DIR/$RELEASE_ID/sapphire/cli-script.php dev/build flush=all;
fi
#permissions
sudo chown -R beanstalk:www-data $DEPLOYMENT_DIR/$RELEASE_ID;
chmod -R 775 $DEPLOYMENT_DIR/$RELEASE_ID/silverstripe-cache;
chmod -R 775 $DEPLOYMENT_DIR/$RELEASE_ID/feeds/cache;
#repointing current site
echo "relinking site $APACHE_DIR to $DEPLOYMENT_DIR/$RELEASE_ID ...";
sudo rm -f $APACHE_DIR;
sudo ln -s $DEPLOYMENT_DIR/$RELEASE_ID $APACHE_DIR;