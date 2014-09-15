<?php

/**
 * Interface INews
 */
interface INews extends IEntity {

    /**
     * @return ISubmitter
     */
    public function getSubmitter();

    public function setSubmitter(ISubmitter $submitter);

    /**
     * @return ITag[]
     */
    public function getTags();

    public function addTag(ITag $tag);


	/**
	 * @return BetterImage
	 */
	public function getImage();

	/**
	 * @param IFileUploadService $upload_service
	 */
	public function registerImage(IFileUploadService $upload_service);
} 