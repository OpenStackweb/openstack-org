Run Local Dev Server
====================

1. Create [.env](.env) file with following properties
```dotenv
GITHUB_OAUTH_TOKEN="<GITHUB TOKEN FROM YOUR GITHUB ACCOUNT>"

SS_DB_HOST=db_model
SS_DATABASE=model
SS_DB_USERNAME=root
SS_DB_PASSWORD=1qaz2wsx

REDIS_HOST=redis
REDIS_PORT=6379
REDIS_DB=0
REDIS_PASSWORD=1qaz2wsx!
REDIS_DATABASES=16
```
2. Create a [_ss_environment.php](_ss_environment.php) file with follwing properties
```php
<?php
/* What kind of environment is this: development, test, or live (ie, production)? */
define('SS_ENVIRONMENT_TYPE', 'dev');

/* Database connection */
define('SS_DATABASE_SERVER', 'db_model');
define('SS_DATABASE_USERNAME', 'root');
define('SS_DATABASE_PASSWORD', '1qaz2wsx');
define('SS_DATABASE_CLASS','CustomMySQLDatabase');
/* Global variables */
$database = 'model';
$email_from = '<YOUR EMAIL>';
$email_log = '<YOUR EMAIL>';
define('SMTPMAILER_SMTP_SERVER_ADDRESS', 'smtp.sendgrid.net'); # SMTP server address
define('SMTPMAILER_DO_AUTHENTICATE', true); # Turn on SMTP server authentication. Set to false for an anonymous connection
define('SMTPMAILER_USERNAME', ''); # SMTP server username, if SMTPAUTH == true
define('SMTPMAILER_PASSWORD', ''); # SMTP server password, if SMTPAUTH == true
define('SENDGRID_API_KEY', '<SENDGRID API KEY>');
```
3. Drop here  [docker-compose/mysql/model](docker-compose/mysql/model) the database dump *.sql file
4. Install docker and docker compose see 
   [https://www.digitalocean.com/community/tutorials/how-to-install-and-use-docker-compose-on-ubuntu-22-04](https://www.digitalocean.com/community/tutorials/how-to-install-and-use-docker-compose-on-ubuntu-22-04) and [https://www.digitalocean.com/community/tutorials/how-to-install-and-use-docker-on-ubuntu-22-04](https://www.digitalocean.com/community/tutorials/how-to-install-and-use-docker-on-ubuntu-22-04)
5. Run script ./start_local_server.sh

Useful Commands
===============

check containers health status

````bash
docker inspect --format "{{json .State.Health }}" www-openstack-model-db-local | jq '.
````

