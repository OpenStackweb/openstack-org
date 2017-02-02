# global variables

$os_db_user          = hiera('os_db_user')
$os_db_password      = hiera('os_db_password')
$mysql_root_password = hiera('mysql_root_password')
$os_db               = hiera('os_db_name')
$developer_email     = hiera('developer_email')
$db_dump_url         = hiera('db_dump_url')

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
  'mysql-client-core-5.6',
  'python-software-properties',
]

# php packages needed for server
$php5_packages = [
  'php5.6-fpm',
  'php5.6-mcrypt',
  'php5.6-cli',
  'php5.6-curl',
  'php5.6-gd',
  'php5.6-json',
  'php5.6-gmp',
  'php5.6-mysqlnd',
  'php5.6-mbstring',
  'php5.6-xml',
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

exec{ 'add-php-5.6-repository':
  cwd       => '/',
  path      => '/usr/bin:/bin:/usr/local/bin:/usr/lib/node_modules/npm/bin',
  logoutput => on_failure,
  command   => "add-apt-repository ppa:ondrej/php && apt-get update",
  require   => Package[$main_packages]
}

package { $php5_packages:
  ensure  => present,
  require => [
    Package[$main_packages],
    Exec['add-php-5.6-repository'],
  ],
}

$override_options = {
  'mysqld' => {
    'bind-address' => '0.0.0.0',
  }
}

class { '::mysql::server':
  package_name            => 'mysql-server-5.6',
  root_password           => $mysql_root_password,
  remove_default_accounts => true,
  override_options        => $override_options
}

class { '::mysql::client':
  package_name => 'mysql-client-5.6',
  require => [
    Package[$main_packages],
    Class['::mysql::server']
  ],
}

# process db from cdn
exec { 'download-db':
  cwd       => '/',
  path      => '/usr/bin:/bin:/usr/local/bin:/usr/lib/node_modules/npm/bin',
  logoutput => on_failure,
  command   => "wget $db_dump_url",
  require   => Package[$main_packages]
}

exec { 'unzip-db':
  cwd       => '/',
  path      => '/usr/bin:/bin:/usr/local/bin:/usr/lib/node_modules/npm/bin',
  logoutput => on_failure,
  command   => 'unzip -o dbdump-current.zip',
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
  command   => "cat /var/www/local.openstack.org/scripts/insert_default_admin.sql >> /dump.sql",
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

file { '/var/www/local.openstack.org/_ss_environment.php':
  ensure  => present,
  content => template('site/_ss_environment.php.erb'),
  owner   => 'vagrant',
  group   => 'www-data',
  mode    => '0640',
}

service { 'nginx':
  ensure    => running,
  require   => [
    Package[$main_packages] ,		
    Package[$php5_packages] ,
    Service['mysql'],
    File['/var/www/local.openstack.org/_ss_environment.php'],
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

file { '/etc/nginx/php5-fpm.conf':
  ensure  => present,
  content => template('site/php5-fpm.conf.erb'),
  owner   => 'vagrant',
  group   => 'www-data',
  mode    => '0640',
  require => Service['nginx'],
}

file { '/etc/nginx/sites-available/local.openstack.org':
  ensure  => present,
  content => template('site/local.openstack.org.erb'),
  owner   => 'vagrant',
  group   => 'www-data',
  mode    => '0640',
  require =>  [
    File['/etc/nginx/silverstripe.conf'],
    File['/etc/nginx/php5-fpm.conf'],
  ]
}

file { '/etc/nginx/sites-enabled/local.openstack.org':
  ensure    => 'link',
  target    => '/etc/nginx/sites-available/local.openstack.org',
  require   => File['/etc/nginx/sites-available/local.openstack.org'],
}

cron { 'RssEventsDigestTask':
    ensure  => 'present',
    command => 'php /var/www/www.openstack.org/framework/cli-script.php /RssEventsDigestTask',
    user => 'root', 
    minute => '*/5', 
}

cron { 'RssNewsDigestTask':
    ensure  => 'present',
    command => 'php /var/www/www.openstack.org/framework/cli-script.php /RssNewsDigestTask',
    user => 'root', 
    minute => '*/5', 
}

cron { 'NewsArticlesUpdateTask':
    ensure  => 'present',
    command => 'php /var/www/www.openstack.org/framework/cli-script.php /NewsArticlesUpdateTask',
    user => 'root', 
    minute => '*/5', 
}

cron { 'UpdateFeedTask':
    ensure  => 'present',
    command => 'php /var/www/www.openstack.org/framework/cli-script.php /UpdateFeedTask',
    user => 'root', 
    minute => '*/5', 
}

swap_file::files { 'default':
  ensure   => present,
}