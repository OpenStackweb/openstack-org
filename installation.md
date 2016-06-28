---
layout: default
title: Welcome to OpenStack Foundation Website
---

## Requirements

The following base components are required to run the application:

* Apache 1.3 or greater
* MySQL 5.0 or greater
* PHP 5.2.0 or greater (PHP7 does not work with SS3)
* PHP_Curl package
* PHP_GD package
* PHP_gmp package
* PHP_dom package

Example installation of dependencies for Ubuntu 14.04:
```
  apt-get  install mariadb-server mariadb-client
  mysql_secure_installation
  apt-get install apache2
  apt-get install php5 php5-curl php5-gd php5-gmp php-dompdf php5-mysql libapache2-mod-php5
```

## Local Installation Procedure

* Get the code and place it under /var/www/openstack
  ```
  cd /var/www
  git clone https://github.com/OpenStackweb/openstack-org openstack
  ```

* Run composer to solve dependencies

   ```
   curl -sS https://getcomposer.org/installer | php
   ```
   
   ```
   php composer.phar install
   ```
   
   ```
   php composer.phar dump-autoload --optimize
   ```

* Get the database dump from [http://219ce3a47922f82273e7-ab6defd935ab43e677f8278246e07e36.r82.cf1.rackcdn.com/dbdump-current.zip](http://219ce3a47922f82273e7-ab6defd935ab43e677f8278246e07e36.r82.cf1.rackcdn.com/dbdump-current.zip)

* Create a database and restore the dump into it
  ```
  mysql -u root -p
  > CREATE DATABASE openstack;
  > GRANT ALL PRIVILEGES ON openstack.* TO 'openstack'@'localhost' IDENTIFIED BY 'OPENSTACK_DBPASS';
  > GRANT ALL PRIVILEGES ON openstack.* TO 'openstack'@'%' IDENTIFIED BY 'OPENSTACK_DBPASS';
  
  wget http://219ce3a47922f82273e7-ab6defd935ab43e677f8278246e07e36.r82.cf1.rackcdn.com/dbdump-current.zip
  unzip dbdump-current.zip
  mysql -u openstack -p < www.openstack.org-purged-backup-db-Jun-27-2017_22-00-02.sql 
  
  ```

* Edit the file sample._ss_environment.php to adjust the following lines to match your environment and save it as _ss_environment.php
   ```
   define('SS_DATABASE_SERVER', 'localhost');
   define('SS_DATABASE_USERNAME', 'root');
   define('SS_DATABASE_PASSWORD', 'admin');
   $database = 'openstack';
   $_FILE_TO_URL_MAPPING['/var/www/openstack'] = 'http://localhost';
   ```

* Rename file _sample.htaccess_ to _.htaccess_

* Edit the file /etc/apache2/sites-available/_default.conf_ to ensure the following content

   ```
   <VirtualHost *:80>
      <Directory /var/www/openstack>
          Options FollowSymLinks
          AllowOverride All
          Order allow,deny
          allow from all
       </Directory>
       RewriteEngine On
       ServerAdmin smarcet@gmail.com
       DocumentRoot "/var/www/openstack"
       ErrorLog "${APACHE_LOG_DIR}/openstack.log"
      CustomLog "${APACHE_LOG_DIR}/openstack.log" common
   </VirtualHost>
   ```
* Ensure mod_rewrite is enabled
   ```
   a2enmod rewrite
   ```

* Run the following command to populate database

   ```
    php /var/www/openstack/framework/cli-script.php dev/build flush=1
   ```
* Restart apache

## Containers Installation Procedure

The Vagrant file is located here: https://github.com/OpenStackweb/openstack-org/blob/master/Vagrantfile

For a clean installation

*  git clone https://github.com/OpenStackweb/openstack-org.git
*  Be sure that your .ssh/config file contains following info (this is neccesary for the private-assets submodule)

   ```
   #private assets
   Host assets.github.com
       HostName github.com
       PreferredAuthentications publickey
       IdentityFile <path_2_your_private_key>
    ```
    
*  run 

   ```
   git submodule init
    ```
*  run 

   ```
   git submodule update
    ```
* Once you installed virtualbox (https://www.virtualbox.org/) and vagrant(https://www.vagrantup.com/downloads.html) on your local environmen, run 

   ```
   vagrant up
    ```
* This will create a virtual machine with a local mysql sql using the purged dump located here http://219ce3a47922f82273e7-ab6defd935ab43e677f8278246e07e36.r82.cf1.rackcdn.com/dbdump-current.zip
    
* Site is hosted on nginx under hostheader "local.openstack.org", your local path (site root) is mapped to /var/www/local.openstac.org on guest machine (VM). VM ip is set to "192.168.33.10" so after vm installation you must add following entry to your host file

   ```
   192.168.33.10 local.openstack.org
    ```

* In order to ssh to VM you have 2 choices

   - On site root on your host machine run

   ```
   vagrant ssh
    ```

   - using an ssh client

   ```
   ssh vagrant@127.0.0.1 -p 2222
    ```

   (password: vagrant)

* Go to the local directory of your site, run the following to get your vagrant ssh private key 

   ```
   vagrant ssh-config
    ```

* If you want to view your local database via a MySQL client like Sequel Pro, go to the local directory of your site and run

   ```
   ssh -i {/LOCALPATH TO OS WEBSITE}/www/openstack-org/.vagrant/machines/default/virtualbox/private_key -p 2222 vagrant@127.0.0.1 -L 3307:127.0.0.1:3306
    ```

 Your SQL client should use a Standard connection:
    - Host: localhost
    - Username: root
    - Password: root
    - Database: os_local
    - Port: 3306

## About the database
OpenStack will provide a db dump on a weekly basis, purged of protected data. The database will create one default admin user. All other data will need to be populated by the user.
