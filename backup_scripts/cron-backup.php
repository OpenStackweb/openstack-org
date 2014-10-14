<?php
###############################################################
# Cron Backup Script
###############################################################
# Developed by Jereme Hancock for Cloud Sites
###############################################################

// specify namespace
namespace OpenCloud;

// require Cron Backup Config
require_once('cron-backup-config.php');
set_time_limit(0);
// Set the date and name for the backup files
date_default_timezone_set('America/Chicago');
$date             = date("M-d-Y_H-i-s");
$backupname_site  = "$url-backup-site-$date.zip";
$backupname_db    = "$url-backup-db-$date.zip";
$backupname_db_wp = "$url-backup-db-wp-$date.zip";

// Check for newer versions of script
function file_get_data($url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

$latest_version = file_get_data('https://raw.github.com/jeremehancock/cron-backup-script-setup/master/version.txt');
$latest_version = preg_replace("/\r|\n/", "", $latest_version);

if (!isset($installed_version)) {
	echo "New version available!\nTo update simply re-install following instructions found here: http://www.rackspace.com/knowledge_center/article/scheduled-backup-cloud-sites-to-cloud-files\n";
} elseif ($installed_version < $latest_version) {
	echo "New version available!\nTo update simply re-install following instructions found here: http://www.rackspace.com/knowledge_center/article/scheduled-backup-cloud-sites-to-cloud-files\n";
}

if ($db_backup == "true") {
// Check mysql database credentials
	$db_connection = mysql_connect($db_host, $db_user, $db_password);

	if (!$db_connection) {
		echo date("h:i:s") . " -- Database connection failed! Check your database credentials in the cron-backup-config.php file.\n";
		die();
	} else {
// Dump the mysql database
		echo date("h:i:s") . " -- Starting database dump...\n";
// First we ensure we are in the root of the site and not the root of account. This is needed since cron runs from the root of the account
		chdir("$path");
		chdir("../../");
		shell_exec("mysqldump -h $db_host -u $db_user --password='$db_password' $db_name > db_backup.sql");
		echo date("h:i:s") . " -- Finished database dump...\n";

		if(isset($db_hot_spare_host)){
			echo date("h:i:s") . " -- Restoring database dump on $db_hot_spare_host hot spare ...\n";
			shell_exec("mysql -h $db_hot_spare_host -u $db_user --password='$db_password' $db_name < db_backup.sql");
			echo date("h:i:s") . " -- restored  database dump on $db_hot_spare_host hot spare...\n";
		}

		echo date("h:i:s") . " -- zipping db backup...\n";
		shell_exec("zip -9pr $backupname_db db_backup.sql");
		echo date("h:i:s") . " -- Database dump complete!\n";
	}

	// WP DB Backup
	$db_connection2 = mysql_connect($db_wp_host, $db_wp_user, $db_wp_password);

	if (!$db_connection2) {
		echo date("h:i:s") . " -- WP Database connection failed! Check your database credentials in the cron-backup-config.php file.\n";
		die();
	} else {
// Dump the mysql database
		echo date("h:i:s") . " -- Starting WP database dump...\n";
// First we ensure we are in the root of the site and not the root of account. This is needed since cron runs from the root of the account
		chdir("$path");
		chdir("../../");
		shell_exec("mysqldump -h $db_wp_host -u $db_wp_user --password='$db_wp_password' $db_wp_name > db_wp_backup.sql");

		echo date("h:i:s") . " -- zipping db WP backup...\n";
		shell_exec("zip -9pr $backupname_db_wp db_wp_backup.sql");
		echo date("h:i:s") . " -- WP Database dump complete!\n";
	}
}

// Backup site files
echo date("h:i:s") . " -- Starting files backup...\n";
chdir("$path");
shell_exec("zip -9pr ../../$backupname_site .");
chdir("../../");

if (file_exists("$backupname_site")) {
	echo date("h:i:s") . " -- Files backup complete!\n";
} else {
	echo date("h:i:s") . " -- File backup failed! Be sure your site is not over 4 gigs.\n";
	if ($db_backup == "true") {
		shell_exec("rm db_backup.sql");
	}
	die();
}


// md5 for local backup
$md5_1 = md5_file($backupname_site);
$md5_2 = md5_file($backupname_db);
$md5_3 = md5_file($backupname_db_wp);

// Set API Timeout
define('RAXSDK_TIMEOUT', '3600');

// require Cloud Files API
require_once('cron-backup-api/lib/rackspace.php');

// Authenticate to Cloud Files
echo date("h:i:s") . " -- Connecting to Cloud Files\n";
try {
	define('AUTHURL', 'https://identity.api.rackspacecloud.com/v2.0/');
	$mysecret = array(
		'username' => $username,
		'apiKey' => $key
	);

	echo date("h:i:s") . " -- Connected to Cloud Files!\n";
// establish our credentials
	$connection = new Rackspace(AUTHURL, $mysecret);
// now, connect to the ObjectStore service

	$ostore = $connection->ObjectStore('cloudFiles', "$datacenter");
} catch (HttpUnauthorizedError $e) {
	if ($db_backup == "true") {
		echo date("h:i:s") . " -- Cloud Files API connection could not be established! Check your API credentials in the cron-backup-config.php file.\n";
		shell_exec("rm $backupname_site ; rm db_backup.sql; rm $backupname_db");
		die();
	} else {
		echo date("h:i:s") . " -- Cloud Files API connection could not be established! Check your API credentials in the cron-backup-config.php file.\n";
		shell_exec("rm $backupname_site");
		die();
	}
}

echo date("h:i:s") . " -- Creating Cloud Files Container...\n";
// create container if it doesn't already exist
$cont = $ostore->Container();
$cont->Create(array('name' => "$url-cron-backups"));

echo date("h:i:s") . " -- Cloud Files container created or already exists!\n";

echo date("h:i:s") . " -- Moving backup to Cloud Files...\n";
// set zipit object
$obj = $cont->DataObject();

echo date("h:i:s") . " -- Moving $backupname_site to Cloud Files...\n";
$obj->Create(array('name' => $backupname_site, 'content_type' => 'application/x-gzip'), $filename = $backupname_site);

// get etag(md5)
$etag = $obj->hash;

// compare md5 wih etag
if ($md5_1 != $etag) {
	$obj->Delete(array('name' => $backupname_site));
	echo date("h:i:s") . " -- Backup failed integrity check! Please try again.\n";
} else {
	echo date("h:i:s") . " -- $backupname_site moved to Cloud Files Successful!\n";
}


echo date("h:i:s") . " -- Moving $backupname_db to Cloud Files...\n";
$obj->Create(array('name' => $backupname_db, 'content_type' => 'application/x-gzip'), $filename = $backupname_db);

// get etag(md5)
$etag = $obj->hash;

// compare md5 wih etag
if ($md5_2 != $etag) {
	$obj->Delete(array('name' => $backupname_db));
	echo date("h:i:s") . " -- Backup failed integrity check! Please try again.\n";
} else {
	echo date("h:i:s") . " -- $backupname_db moved to Cloud Files Successful!\n";
}

//WP DB BACKUP

echo date("h:i:s") . " -- Moving $backupname_db_wp to Cloud Files...\n";
$obj->Create(array('name' => $backupname_db_wp, 'content_type' => 'application/x-gzip'), $filename = $backupname_db_wp);

// get etag(md5)
$etag = $obj->hash;

// compare md5 wih etag
if ($md5_3 != $etag) {
	$obj->Delete(array('name' => $backupname_db_wp));
	echo date("h:i:s") . " -- Backup failed integrity check! Please try again.\n";
} else {
	echo date("h:i:s") . " -- $backupname_db_wp moved to Cloud Files Successful!\n";
}

if ($db_backup == "true") {
	echo date("h:i:s") . " -- Cleaning up local backups...\n";
	//After your backup has been uploaded, remove the zip from the filesystem.
	shell_exec("rm $backupname_site ; rm db_backup.sql; db_wp_backup.sql; rm $backupname_db; rm $backupname_db_wp");
	echo date("h:i:s") . " -- Local backups cleaned up!\n";
} else {
	shell_exec("rm $backupname_site");
}

$line = date("h:i:s") . " -- Backup complete!\n";
mail('openstack@tipit.net', 'OS Site Backup Complete!', $line);
echo $line;
?>
