<?php

/**
 * Class SummitVideoProcessingTask
 */
class SummitVideoProcessingTask extends CronTask
{
    /**
     * @var
     */
    protected $api;

    /**
     * @var int
     */
    protected $videosUpdated = 0;

    /**
     * SummitVideoProcessingTask constructor.
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
        echo sprintf('%s - SummitVideoProcessingTask::run', date("Y-m-d H:i:s")).PHP_EOL;

        SapphireTransactionManager::getInstance()->transaction(function () {
            $unprocessedVideos = PresentationVideo::get()
                ->filter([
                    'Processed' => false
                ])
                ->sort("ID", "DESC")
                ->limit(50);

            if (!$unprocessedVideos->exists()) {
                echo sprintf('%s - SummitVideoProcessingTask::run unprocessed videos not found', date("Y-m-d H:i:s")).PHP_EOL;
                return 0;
            }

            if (isset($_GET['force'])) {
                $maxAge = 12*30*24*60*60; // 1 year
            } else {
                $maxAge = SummitVideoApp::config()->abandon_unprocessed_videos_after;
            }

            $ids = [];

            foreach ($unprocessedVideos as $video) {

                $summit = $video->Presentation()->Summit();

                $dateUTC = $summit->convertDateFromTimeZone2UTC(
                    SS_DateTime::now()->Rfc2822()
                );

                $dateUTCTimestamp = strtotime($dateUTC);
                $age = $dateUTCTimestamp - strtotime($video->DateUploaded);

                echo sprintf('%s - SummitVideoProcessingTask::run processing video id %s (%s) age %s from summit %s', date("Y-m-d H:i:s"), $video->ID , $video->Title,$age, $summit->ID ).PHP_EOL;

                if ($age > $maxAge) {
                    echo sprintf('%s - SummitVideoProcessingTask::run processing video id %s from summit %s has been unprocessed for a long time  (%s seconds). It should be deleted.', date("Y-m-d H:i:s"), $video->ID , $summit->ID, $age ).PHP_EOL;
                    continue;
                }

                if ($video->YouTubeID) {
                    $ids[] = $video->YouTubeID;
                }

            }

            $response = false;

            try {
                if ($ids) {
                    $response = $this->api->getVideoStatusById($ids);
                }
            } catch (\Exception $e) {
                echo sprintf('%s - SummitVideoProcessingTask::run YouTube check for status failed %s', date("Y-m-d H:i:s"), $e->getMessage()).PHP_EOL;
                return -1;
            }

            if (!$response) return false;

            $body = $response->getBody()->getContents();
            $data = Convert::json2array($body);
            $items = $data['items'];

            if (empty($items)) {
                echo sprintf('%s - SummitVideoProcessingTask::run No videos are marked as processing. Exiting.', date("Y-m-d H:i:s")).PHP_EOL;
                return -1;
            }

            foreach ($items as $item) {
                $currentStatus = $item['status']['uploadStatus'];
                if ($currentStatus == 'processed') {
                    $video = PresentationVideo::get()->filter([
                        'YouTubeID' => $item['id']
                    ])->first();

                    if (!$video) {
                        echo sprintf('%s - SummitVideoProcessingTask::run Tried to update processing status for %s but no PresentationVideo with that YouTubeID was found.',
                                date("Y-m-d H:i:s"),
                                $item['id']).PHP_EOL;
                        continue;
                    }

                    $video->Processed = true;
                    $video->write();
                    echo sprintf('%s - SummitVideoProcessingTask::run marking video %s as processed ', date("Y-m-d H:i:s"), $video->ID).PHP_EOL;
                    $this->videosUpdated++;
                }

            }

            echo sprintf('%s - SummitVideoProcessingTask::run %s videos updated ', date("Y-m-d H:i:s"), $this->videosUpdated).PHP_EOL;
            return 0;
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
