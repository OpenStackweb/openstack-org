## Overview

The OpenStack Foundation Website

WHAT IS IT?
openstack.org runs a PHP web application called Silverstripe. More about the Silverstripe CMS is available here: http://silverstripe.org/

This repository is designed to help other project members develop, test, and contribute to the openstack.org website project. Note that this project is only for the public openstack.org website and content. To participate in building the actual OpenStack software, go to:
http://wiki.openstack.org/HowToContribute

WHO DO I CONTACT WITH QUESTIONS?
Please contact Todd Morey: todd@openstack.org. I'll be happy to help in any way that I can.

WHAT'S INCLUDED
Included in this repository are:

Third Party:
- The Silverstripe CMS v 3.1.x (for easy of deployment)

WHAT'S NOT INCLUDED
- Images - You'll note many missing images throughout the site. This is due to one of the following: copyright restrictions, OpenStack sponsors, file size restrictions.


REQUIREMENTS FOR DEPLOYING OPENSTACK.ORG

To run the openstack.org website, the server environment needs:
- Apache 1.3 or greater
- PHP 5.2.0 or greater
- MySQL 5.0 or greater

INSTALLATION

openstack.org website uses composer (https://getcomposer.org/) to manage all dependencies

to install run following commands

* curl -sS https://getcomposer.org/installer | php
* php composer.phar install
* php composer.phar dump-autoload --optimize
* chmod 777 -R  vendor/ezyang/htmlpurifier/library/HTMLPurifier/DefinitionCache/Serializer

DATABASE
OpenStack will provide a db dump on a weekly basis, purged of protected data. The dump can be found ******* (to add public Cloud Files location). The database will create one default admin user. All other data will need to be populated by the user.

TODO:
We need a way to provide a clean and current dump of the database, since a majority of the web content for the site is stored there. Also, we need detailed installation instructions to run the site locally on LAMP or MAMP.
