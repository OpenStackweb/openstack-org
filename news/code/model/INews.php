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

    public function clearTags();

	/**
	 * @param IFileUploadService $upload_service
	 */
	public function registerImage(IFileUploadService $upload_service);

    /**
     * @param IFileUploadService $upload_service
     */
    public function registerDocument(IFileUploadService $upload_service);

    /**
     * @param NewsSubmitter $submitter
     * @return void
     */
    public function registerSubmitter(NewsSubmitter $info);

    /**
     * @param string[] $tags
     * @return void
     */
    public function registerTags($tags);
} 