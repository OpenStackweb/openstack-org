<?php

/**
 * Interface IFileUploadService
 */
interface IFileUploadService {
	/**
	 * @param string $folder_name
	 */
	public function setFolderName($folder_name);

	/**
	 * @param string  $file_name
	 * @param IEntity $entity
	 * @return IEntity
	 */
	public function upload($file_name, IEntity $entity);
} 