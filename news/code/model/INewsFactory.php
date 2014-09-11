<?php

interface INewsFactory {
	/**
	 * @param NewsMainInfo $info
	 * @param string[] $tags
	 * @param integer $submitter
	 * @return INews
	 */
	public function buildNews(NewsMainInfo $info, $tags, $submitter);

	/**
	 * @param array $data
	 * @return NewsMainInfo
	 */
	public function buildNewsMainInfo(array $data);

} 