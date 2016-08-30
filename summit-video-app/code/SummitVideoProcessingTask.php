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
        SapphireTransactionManager::getInstance()->transaction(function () {
            $unprocessedVideos = PresentationVideo::get()
                ->filter([
                    'Processed' => false
                ])
                ->limit(50);

            if (!$unprocessedVideos->exists()) {
                return;
            }

            $summit = Summit::get_active();
            $dateUTC = $summit->convertDateFromTimeZone2UTC(
                SS_DateTime::now()->Rfc2822()
            );
            $dateUTCTimestamp = strtotime($dateUTC);
            $maxAge = SummitVideoApp::config()->abandon_unprocessed_videos_after;
            $ids = [];

            foreach ($unprocessedVideos as $video) {
                $age = $dateUTCTimestamp - strtotime($video->DateUploaded);
                if ($age > $maxAge) {
                    SS_Log::log("Video {$video->Title} has been unprocessed for a long time. ($age seconds). It should be deleted.",
                        SS_Log::WARN);
                    continue;
                }

                $ids[] = $video->YouTubeID;
            }

            try {
                $response = $this->api->getVideoStatusById($ids);
            } catch (\Exception $e) {
                SS_Log::log("YouTube check for status failed" . $e->getMessage(), SS_Log::ERR);
                return;
            }

            $body = $response->getBody()->getContents();
            $data = Convert::json2array($body);
            $items = $data['items'];

            if (empty($items)) {
                echo "No videos are marked as processing. Exiting.\n";
                return;
            }

            foreach ($items as $item) {
                $currentStatus = $item['status']['uploadStatus'];
                if ($currentStatus == 'processed') {
                    $video = PresentationVideo::get()->filter([
                        'YouTubeID' => $item['id']
                    ])->first();

                    if (!$video) {
                        SS_Log::log("Tried to update processing status for " . $item['id'] . " but no PresentationVideo with that YouTubeID was found.",
                            SS_Log::WARN);
                        continue;
                    }

                    $video->Processed = true;
                    $video->write();
                    $this->videosUpdated++;
                }

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
