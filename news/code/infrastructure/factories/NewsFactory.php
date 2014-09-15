<?php

/**
 * Class NewsFactory
 */
final class NewsFactory
	implements INewsFactory {

    /**
     * @param NewsMainInfo $info
     * @param string[] $tags
     * @param $submitter
     * @return INews
     */
	public function buildNews(NewsMainInfo $info, $tags, $submitter) {
		$news = new News();
        $news->registerMainInfo($info);
		$news->registerTags($tags);
        if (get_class($submitter) == 'NewsSubmitter') {
            $news->registerSubmitter($submitter);
        } else {
            $news->addSubmitter($submitter);
        }

		return $news;
	}

	/**
	 * @param array $data
	 * @return NewsMainInfo
	 */
	public function buildNewsMainInfo(array $data)
	{
        $main_info = new NewsMainInfo(trim($data['headline']),trim($data['summary']), $data['date'],
                                      trim($data['body']),$data['link'],$data['image'],$data['document'],
                                      $data['date_embargo'],$data['date_expire']);
		return $main_info;
	}

    /**
     * @param array $data
     * @return NewsSubmitter
     */
    public function buildNewsSubmitter(array $data)
    {
        $submitter = new NewsSubmitter(trim($data['submitter_first_name']),trim($data['submitter_last_name']), trim($data['submitter_email']),
                                       trim($data['submitter_company']),$data['submitter_phone']);

        return $submitter;
    }

}