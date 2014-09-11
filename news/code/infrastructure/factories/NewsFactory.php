<?php

/**
 * Class NewsFactory
 */
final class NewsFactory
	implements INewsFactory {

    /**
     * @param NewsMainInfo $info
     * @param string[] $tags
     * @param integer $submitter
     * @return INews
     */
	public function buildNews(NewsMainInfo $info, $tags, $submitter) {
		$news = new News();
        $news->registerMainInfo($info);
		$news->registerTags($tags);
		$news->registerSubmitter($submitter);

		return $news;
	}

	/**
	 * @param array $data
	 * @return NewsMainInfo
	 */
	public function buildNewsMainInfo(array $data)
	{
		$main_info = new NewsMainInfo(trim($data['headline']),trim($data['summary']), $data['datetime'],
                                      trim($data['body']),$data['link'],$data['image'],$data['document'],
                                      $data['embargo_date'],$data['rank'],$data['slider'],$data['featured'],$data['expire_date']);
		return $main_info;
	}

}