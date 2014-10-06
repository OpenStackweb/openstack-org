<?php
/**
 * this is a helper script to clean up former deployments
 */

define('DEPLOYMENT_FOLDER_TO_KEEP',2);

if(count($argv) != 2){
	echo 'missing deployment path parameter!'.PHP_EOL;
	die();
}

$deployment_path = $argv[1];

if(!file_exists($deployment_path) || !is_dir($deployment_path)){
	echo 'deployment path '.$deployment_path.' does not exists!'.PHP_EOL;
	die();
}

function scan_dir($dir) {
	$ignored = array('.', '..');

	$files = array();

	foreach (scandir($dir) as $file) {
		if (in_array($file, $ignored) || !isValidDeploymentDir($file)) continue;
		$files[$file] = filemtime($dir . '/' . $file);
	}

	asort($files);
	$files = array_keys($files);

	return ($files) ? $files : false;
}

function isValidDeploymentDir($file_name){
	$pattern     = '@^\d+$@i';
	$res         = preg_match($pattern, $file_name,$results);
	return $res == 1;
}

$files  = scan_dir($deployment_path);

echo 'got '.count($files).' former deployments folders.'.PHP_EOL;

if(count($files) <= DEPLOYMENT_FOLDER_TO_KEEP){
	echo 'there are not deployment folders to clean up..'.PHP_EOL;
	die();
}

//delete older deployments and left DEPLOYMENT_FOLDER_TO_KEEP qty

for($i=0; $i < count($files) - DEPLOYMENT_FOLDER_TO_KEEP ; $i++) {
	$dir = $deployment_path . '/' . $files[$i];
	echo 'deleting deployment folder '.$dir.PHP_EOL;
	shell_exec('sudo rm -R ' .$dir );
}
