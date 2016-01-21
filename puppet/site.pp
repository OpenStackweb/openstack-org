# global variables

$os_db_user      = hiera('os_db_user')
$os_db_password  = hiera('os_db_password')
$os_db           = 'os_local'
$developer_email = 'test@test.com'

$main_packages = [
  'curl',
  'wget',
  'build-essential',
  'git',
  'geoip-bin',
  'sendmail',
  'mysql-server',
  'zip',
  'unzip',
]

# php packages needed for server
$php5_packages = [
  'php5-fpm',
  'php5-mcrypt',
  'php5-curl',
  'php5-gd',
  'php5-json',
  'php5-gmp',
  'php5-mysqlnd',
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

package { $php5_packages:
  ensure  => present,
  require => [
    Package[$main_packages],
  ],
}

service { "mysql":
  ensure  => running,
  require => Package[$main_packages]
}

exec { 'download-db':
  cwd       => '/',
  path      => '/usr/bin:/bin:/usr/local/bin:/usr/lib/node_modules/npm/bin',
  logoutput => on_failure,
  command   => 'wget http://219ce3a47922f82273e7-ab6defd935ab43e677f8278246e07e36.r82.cf1.rackcdn.com/dbdump-current.zip',
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

#create and import db
mysql::db { $os_db :
  user           => $os_db_user,
  password       => $os_db_password,
  host           => 'localhost',
  grant          => ['ALL'],
  sql            => '/dump.sql',
  import_timeout => 900,
  require        => [
    Service['mysql'],
    Exec['rename-db'],
  ],
}

file { '/var/www/local.openstack.org/_ss_environment.php':
  ensure  => present,
  content => template('site/_ss_environment.php.erb'),
  owner   => 'vagrant',
  group   => 'www-data',
  mode    => '0640',
}

class { 'nginx':
  require => [
    Package[$php5_packages] ,
    Service['mysql'],
    File['/var/www/local.openstack.org/_ss_environment.php'],
  ],
}

file { '/etc/nginx/ssl':
  ensure    => 'link',
  target    => '/etc/ssl_certs',
  require   => Class['nginx'],
}

file { '/etc/nginx/silverstripe.conf':
  ensure  => present,
  content => template('site/silverstripe.conf.erb'),
  owner   => 'vagrant',
  group   => 'www-data',
  mode    => '0640',
  require => Class['nginx'],
}

file { '/etc/nginx/php5-fpm.conf':
  ensure  => present,
  content => template('site/php5-fpm.conf.erb'),
  owner   => 'vagrant',
  group   => 'www-data',
  mode    => '0640',
  require => Class['nginx'],
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

file { '/var/www/local.openstack.org/themes/openstack/images':
  ensure    => 'link',
  target    => '/var/www/local.openstack.org/private-assets/themes/openstack/images',
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
