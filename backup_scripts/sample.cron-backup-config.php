<?php 
###############################################################
# Cron Backup Script
###############################################################
# Developed by Jereme Hancock for Cloud Sites
###############################################################
//Set information specific to your site
$db_host = "";
$db_user = "";
$db_password = "";
$db_name = "";
//WP DB INFO
$db_wp_host = "";
$db_wp_user = "";
$db_wp_password = "";
$db_wp_name = "";
//domain;
$url = "www.openstack.org";
//full server path to site
$path = "";
$datacenter = "";

//Set your Cloud Files API credentials
$username = "";
$key = "";

// Backup Database? This checks if the database credentials are empty and if so the script will skip the database backup
if (!empty($db_host) && !empty($db_user) && !empty($db_password) && !empty($db_name)) {
$db_backup = "true";
}
else {
$db_backup = "false";
}

$installed_version = "1.1";

?>