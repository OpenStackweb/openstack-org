<?php

/**
 * Class SummitVideoViewTask
 */
class SummitVideoViewTask extends CronTask
{

    /**
     * @var
     */
    protected $api;

    /**
     * @var int
     */
    protected $sanityCheck = 0;

    /**
     * @var int
     */
    protected $videosUpdated = 0;

    /**
     * SummitVideoViewTask constructor.
     * @param $api
     */
    public function __construct($api)
    {
        $this->api = $api;

        parent::__construct();
    }


    /**
     *
     */
    public function run()
    {
        SapphireTransactionManager::getInstance()->transaction(function () {

            $pageToken = null;

            while (true) {

                if ($this->videosUpdated >= SummitVideoApp::config()->popular_video_limit) {
                    break;
                }
                // Prevent an infinite loop if the YouTube service is acting strange

                if ($this->sanityCheck === 5) {
                    $e = new Exeception('Task has run too many times. Seems to be an infinite loop. Could be something wrong with the YouTube service?');
                    SS_Log::log($e, SS_Log::ERR);
                    throw $e;
                }

                try {
                    $response = $this->api->getPopularVideos($pageToken);
                } catch (\Exception $e) {
                    SS_Log::log("YouTube Search failed" . $e->getMessage(), SS_Log::ERR);
                }

                $this->sanityCheck++;
                $body = $response->getBody()->getContents();
                $data = Convert::json2array($body);
                $nextPageToken = @$data['nextPageToken'];
                $ids = [];

                foreach ($data['items'] as $item) {
                    if ($item['id']['kind'] === 'youtube#video') {
                        $ids[] = $item['id']['videoId'];
                    }
                }

                try {
                    $response = $this->api->getVideoStatsById($ids);
                } catch (\Exception $e) {
                    SS_Log::log("YouTube video stats failed" . $e->getMessage(), SS_Log::ERR);
                }

                $body = $response->getBody()->getContents();
                $data = Convert::json2array($body);
                foreach ($data['items'] as $v) {
                    $video = PresentationVideo::get()->filter(['YouTubeID' => $v['id']])->first();
                    if ($video) {
                        $video->Views = $v['statistics']['viewCount'];
                        $video->write();
                        $this->videosUpdated++;
                    }
                }

                // If there are no more pages, then bail
                if ($nextPageToken === $pageToken) {
                    break;
                }

                $pageToken = $nextPageToken;
            }

            echo "{$this->videosUpdated} videos updated.\n";
        });
    }


    /**
     * @return int
     */
    public function getVideosUpdated()
    {
        return $this->videosUpdated;
    }

}
