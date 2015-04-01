---
layout: default
title: Welcome to OpenStack Foundation Website
---

## Requirements

The following base components are required to run the application:

* Apache 1.3 or greater
* MySQL 5.0 or greater
* PHP 5.2.0 or greater
* PHP_Curl package
* PHP_GD package
* PHP_gmp package
* PHP_dom package


## Installation Procedure

* Get the code and place it under /var/www/openstack

* Run composer to solve dependencies

   ````
   curl -sS https://getcomposer.org/installer | php
   php composer.phar install
   php composer.phar dump-autoload --optimize
   ````

* Get the database dump from [http://219ce3a47922f82273e7-ab6defd935ab43e677f8278246e07e36.r82.cf1.rackcdn.com/dbdump-current.zip](http://219ce3a47922f82273e7-ab6defd935ab43e677f8278246e07e36.r82.cf1.rackcdn.com/dbdump-current.zip)

* Create a database and restore the dump into it

* Edit the file sample._ss_environment.php to adjust the following lines to match your environment and save it as _ss_environment.php
   ````
   define('SS_DATABASE_SERVER', 'localhost');
   define('SS_DATABASE_USERNAME', 'root');
   define('SS_DATABASE_PASSWORD', 'admin');
   $database = 'openstack';
   $_FILE_TO_URL_MAPPING['/var/www/openstack'] = 'http://localhost';
   ````

* Rename file _sample.htaccess_ to _.htaccess_

* Edit the file /etc/apache2/sites-available/_default.conf_ to ensure the following content
   ````
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
   ````

* Run the following command to populate database
   ````
    _php /var/www/openstack/framework/cli-script.php dev/build flush=1_ 
   ````
* Restart apache

## About the database
OpenStack will provide a db dump on a weekly basis, purged of protected data. The database will create one default admin user. All other data will need to be populated by the user.
