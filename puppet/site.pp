# Copyright (c) 2019 OpenStack Foundation
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#    http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or
# implied.
# See the License for the specific language governing permissions and
# limitations under the License.

# global variables
$os_db_user                         = lookup('os_db_user')
$os_db_password                     = lookup('os_db_password')
$mysql_root_password                = lookup('mysql_root_password')
$os_db                              = lookup('os_db_name')
$developer_email                    = lookup('developer_email')
$db_dump_url                        = lookup('db_dump_url')
$cloud_assets_base_url              = lookup('cloud_assets_base_url')
$cloud_assets_auth_url              = lookup('cloud_assets_auth_url')
$cloud_assets_container_name        = lookup('cloud_assets_container_name')
$cloud_assets_region                = lookup('cloud_assets_region')
$cloud_assets_app_credential_id     = lookup('cloud_assets_app_credential_id')
$cloud_assets_app_credential_secret = lookup('cloud_assets_app_credential_secret')
$cloud_assets_project_name          = lookup('cloud_assets_project_name')

# fact
notice("server_name ${server_name}")

$main_packages = [
  'curl',
  'wget',
  'build-essential',
  'git',
  'geoip-bin',
  'sendmail',
  'zip',
  'unzip',
  'nginx',
  'mysql-client-core-5.7',
  'python-dev',
  'python-pip',
  'libmysqlclient-dev',
  'software-properties-common',
  'nano',
  'apt-transport-https',
  'htop',
  'net-tools',
  'iputils-ping',
  'lsof'
]

# php packages needed for server
$php_packages = [
    'php7.2-fpm',
    'php7.2-curl',
    'php7.2-mysqlnd',
    'php7.2-xml',
    'php7.2-mbstring',
    'php7.2',
    'php7.2-json',
    'php7.2-gd',
    'php7.2-gmp',
    'php7.2-ssh2',
    'php7.2-intl',
    'php7.2-xdebug',
]

exec { 'apt-get update':
  command => '/usr/bin/apt-get update',
  timeout => 0
}

package { $main_packages:
  ensure  => present,
  require => [
    Exec['apt-get update']
  ],
}

package { $php_packages:
  ensure  => present,
  require => [
    Package[$main_packages],
  ],
}

$override_options = {
  'mysqld' => {
    'bind-address' => '0.0.0.0',
  }
}

class { '::mysql::server':
  package_name            => 'mysql-server-5.7',
  root_password           => $mysql_root_password,
  remove_default_accounts => true,
  override_options        => $override_options
}

class { '::mysql::client':
  package_name => 'mysql-client-5.7',
  require => [
    Package[$main_packages],
    Class['::mysql::server']
  ],
}

# force 10.x version
class { '::nodejs':
	repo_url_suffix => '10.x',
}

# process db from cdn
exec { 'download-db':
  cwd       => '/',
  path      => '/usr/bin:/bin:/usr/local/bin:/usr/lib/node_modules/npm/bin',
  logoutput => on_failure,
  command   => "wget $db_dump_url -O /dbdump-current.zip",
  require   => Package[$main_packages]
}

exec { 'unzip-db':
  cwd       => '/',
  path      => '/usr/bin:/bin:/usr/local/bin:/usr/lib/node_modules/npm/bin',
  logoutput => on_failure,
  command   => 'unzip dbdump-current.zip',
  require   => Exec['download-db'],
}

exec { 'rename-db':
  cwd       => '/',
  path      => '/usr/bin:/bin:/usr/local/bin:/usr/lib/node_modules/npm/bin',
  logoutput => on_failure,
  command   => "ls | grep *.sql | tr -d '\n' | xargs -0 -I file mv file /dump.sql",
  require   => Exec['unzip-db'],
}

# append to script the defautl admin user and his group
exec { 'post-process-db':
  cwd       => '/',
  path      => '/usr/bin:/bin:/usr/local/bin:/usr/lib/node_modules/npm/bin',
  logoutput => on_failure,
  command   => "cat /var/www/www.openstack.org/scripts/insert_default_admin.sql >> /dump.sql",
  require   => Exec['rename-db'],
}

# set proper innodb file format
exec { 'mysql-post-install-cmd':
  cwd       => '/',
  path      => '/usr/bin:/bin:/usr/local/bin:/usr/lib/node_modules/npm/bin',
  logoutput => on_failure,
  command   => "mysql -u root --password=$mysql_root_password -e \"SET GLOBAL innodb_file_format_max = 'Barracuda';SET GLOBAL innodb_file_format = 'Barracuda';\"",
  require   => Class['::mysql::client'],
}

#create and import db

mysql::db { $os_db :
  user           => $os_db_user,
  password       => $os_db_password,
  host           => '%',
  grant          => ['ALL'],
  sql            => '/dump.sql',
  import_timeout => 1200,
  require        => [
    Exec['post-process-db'],
    Exec['mysql-post-install-cmd']
  ],
}

file { '/var/www/www.openstack.org/_ss_environment.php':
  ensure  => present,
  content => template("site/_ss_environment.php.erb"),
  owner   => 'vagrant',
  group   => 'www-data',
  mode    => '0640',
}

file { '/var/www/www.openstack.org/openstack/_config/cloudassets.yml':
  ensure  => present,
  content => template("site/cloudassets.yml.erb"),
  owner   => 'vagrant',
  group   => 'www-data',
  mode    => '0640',
}

file { '/etc/php/7.2/fpm/pool.d/www.conf':
  ensure  => present,
  content => template('site/www.conf.erb'),
  owner   => 'vagrant',
  group   => 'www-data',
  mode    => '0640',
  require   => [
    Package[$php_packages] ,
  ],
}

file { '/var/www/www.openstack.org/db.ini':
  ensure  => present,
  content => template('site/db.ini.erb'),
  owner   => 'vagrant',
  group   => 'www-data',
  mode    => '0640',
}

service { 'php7.2-fpm':
  ensure    => running,
  require   => [
    Package[$php_packages] ,
  ],
}

service { 'nginx':
  ensure    => running,
  require   => [
    Package[$main_packages] ,		
    Package[$php_packages] ,
    Service['php7.2-fpm'],
    File['/var/www/www.openstack.org/_ss_environment.php'],
  ],
}

file { '/etc/nginx/ssl':
  ensure    => 'link',
  target    => '/etc/ssl_certs',
  require   => Service['nginx'],
}

file { '/etc/nginx/silverstripe.conf':
  ensure  => present,
  content => template('site/silverstripe.conf.erb'),
  owner   => 'vagrant',
  group   => 'www-data',
  mode    => '0640',
  require => Service['nginx'],
}

file { '/etc/nginx/php-fpm.conf':
  ensure  => present,
  content => template('site/php-fpm.conf.erb'),
  owner   => 'vagrant',
  group   => 'www-data',
  mode    => '0640',
  require => Service['nginx'],
}

file { '/etc/nginx/sites-available/www.openstack.org':
  ensure  => present,
  content => template('site/www.openstack.org.erb'),
  owner   => 'vagrant',
  group   => 'www-data',
  mode    => '0640',
  require =>  [
    File['/etc/nginx/silverstripe.conf'],
    File['/etc/nginx/php-fpm.conf'],
  ]
}

file { '/etc/php/7.2/mods-available/xdebug.ini':
  ensure  => present,
  content => template('site/xdebug.ini.erb'),
  owner   => 'vagrant',
  group   => 'www-data',
  mode    => '0640',
  require =>  [
    Package[$php_packages],
  ]
}

file { '/etc/nginx/sites-enabled/www.openstack.org':
  ensure    => 'link',
  target    => '/etc/nginx/sites-available/www.openstack.org',
  require   => File['/etc/nginx/sites-available/www.openstack.org'],
}

#fact
if $use_swap == 1 {
  swap_file::files { 'default':
    ensure => present,
  }
}