<?php

interface INewsFactory {
	/**
	 * @param NewsMainInfo       $info
	 * @param string[]           $tags
	 * @param                    $submitter
	 * @param IFileUploadService $upload_service
	 * @return INews|News
	 */
	public function buildNews(NewsMainInfo $info, $tags, $submitter,  IFileUploadService $upload_service);

	/**
	 * @param array $data
	 * @return NewsMainInfo
	 */
	public function buildNewsMainInfo(array $data);


    /**
     * @param array $data
     * @return NewsSubmitter
     */
    public function buildNewsSubmitter(array $data);

    public function setNewsID(INews $news, array $data);
} 