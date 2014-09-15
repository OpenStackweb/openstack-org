<?php

final class SapphireFileUploadService implements IFileUploadService {

	/**
	 * Upload object (needed for validation
	 * and actually moving the temporary file
	 * created by PHP).
	 *
	 * @var Upload
	 */
	protected $upload;

	/**
	 * @var string
	 */
	private $folder_name;


	public function __construct(){
		$this->upload = new Upload();
	}

	/**
	 * @param string $folder_name
	 */
	public function setFolderName($folder_name){
		$this->folder_name = $folder_name;
	}

	/**
	 * @param string  $file_name
	 * @param IEntity $entity
	 * @return IEntity
	 */
	public function upload($file_name, IEntity $entity){
		if(!isset($_FILES[$file_name])) return false;
		// assume that the file is connected via a has-one
		$hasOnes = $entity->has_one($file_name);
		// try to create a file matching the relation
		$file = (is_string($hasOnes)) ? Object::create($hasOnes) : new File();
		$this->upload->loadIntoFile($_FILES[$file_name], $file, $this->folder_name);
		if($this->upload->isError()) return false;
		$file = $this->upload->getFile();
		if(!$hasOnes) return false;
		// save to record
		$entity->{$file_name . 'ID'} = $file->ID;
		return $file;
	}
} 