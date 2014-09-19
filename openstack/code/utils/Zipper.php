<?php
class Zipper{
	
	function CreateZip($files = array(),$destination = '',$filename='',$overwrite = false) {
		//vars
		$valid_files = array();
		//if files were passed in...
		if(is_array($files)) {
			//cycle through each file
			foreach($files as $file) {
				//make sure the file exists
				if(file_exists($file)) {
					$valid_files[] = $file;
				}
			}
		}
		//if we have good files...
		if(count($valid_files)) {
			//create the archive
			$zip = new ZipArchive();
			if(!is_dir($destination)) mkdir($destination, 0775,true);
			if($zip->open($destination.'/'.$filename,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
				return false;
			}
			//add the files
			foreach($valid_files as $file) {
				$zip->addFile($file,basename($file));
			}
			//debug
			//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
			 
			//close the zip -- done!
			$zip->close();
			 
			//check to make sure the file exists
			return file_exists($destination);
		}
		else
		{
			return false;
		}
	}
	
	public function getZipLink($files_to_zip = array(), $folder = '' ,$filename) {
		  if ($files_to_zip) {
	         $result = $this->CreateZip($files_to_zip, $folder,$filename,true);
	         return $result;
	      } else {
	         return false;
	      }
	   }
}