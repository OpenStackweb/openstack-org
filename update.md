---
layout: default
title: Update your install
---

You will periodically need to update your environment. This article explains one way to do so.

## Set up your fork for updating
The first time you update, you will need to run this to let git know where the upstream lives.
```
git remote add upstream https://github.com/Openstackweb/openstack-org.git
```

## Update your fork
These are the basic steps for syncing your fork, and updating your local code to the latest version.

```
git fetch upstream
git checkout master
git merge upstream/master
git push
```

## Update dependencies

```
cd /var/www
php composer.phar update
php composer.phar dump-autoload --optimize
```

## Update the database
* Get the latest database dump from [http://219ce3a47922f82273e7-ab6defd935ab43e677f8278246e07e36.r82.cf1.rackcdn.com/dbdump-current.zip](http://219ce3a47922f82273e7-ab6defd935ab43e677f8278246e07e36.r82.cf1.rackcdn.com/dbdump-current.zip)

* Restore the dump into your database
  
  ```
  wget http://219ce3a47922f82273e7-ab6defd935ab43e677f8278246e07e36.r82.cf1.rackcdn.com/dbdump-current.zip
  unzip dbdump-current.zip
  mysql -u openstack -p < www.openstack.org-purged-backup-db-Jun-27-2017_22-00-02.sql 
  ```


* Run the following command to populate database

   ```
    php /var/www/openstack/framework/cli-script.php dev/build flush=1
   ```
* Restart apache
