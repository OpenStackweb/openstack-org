<?php

/**
 * Class SummitVideoAppBackend
 */
class SummitVideoAppBackend
{

    /**
     * A YouTube API service
     * @var SummitVideoYouTubeService
     */
    protected $youTube;


    /**
     * SummitVideoAppBackend constructor.
     * @param $youtube
     */
    public function __construct($youtube)
    {
        $this->youTube = $youtube;
    }


    /**
     * @param array $params
     * @return array
     */
    public function getVideos($params = [])
    {
        $summit = null;
        $speaker = null;
        $start = isset($params['start']) ? $params['start'] : 0;
        $defaultLimit = SummitVideoApp::config()->default_video_limit;

        $videos = PresentationVideo::get()
            ->filter([
                'DisplayOnSite' => true,
                'Processed' => true
            ])
            ->sort('DateUploaded', 'DESC');

        if (isset($params['summit'])) {
            $summit = Summit::get()->where("Slug = '". $params['summit'] ."'")->first();
            if ($summit) {
                $videos = $videos
                    ->innerJoin('SummitEvent', 'SummitEvent.ID = PresentationMaterial.PresentationID')
                    ->filter('SummitEvent.SummitID', $summit->ID);
            } else {
                $videos = ArrayList::create();
            }
        } else {
            if (isset($params['speaker'])) {
                $speaker = PresentationSpeaker::get()->byID($params['speaker']);
                if ($speaker) {
                    $videos = $videos
                        ->innerJoin('Presentation', 'Presentation.ID = PresentationMaterial.PresentationID')
                        ->innerJoin('Presentation_Speakers', 'Presentation_Speakers.PresentationID = Presentation.ID')
                        ->filter('Presentation_Speakers.PresentationSpeakerID', $speaker->ID);
                } else {
                    $videos = ArrayList::create();
                }
            } else {
                if (isset($params['popular'])) {
                    $videos = $videos->sort('Views DESC');
                } else {
                    if (isset($params['highlighted'])) {
                        $videos = $videos->filter([
                            'Highlighted' => true
                        ]);
                    } else {
                        if (isset($params['search'])) {
                            $videos = $videos
                                ->innerJoin('Presentation', 'Presentation.ID = PresentationMaterial.PresentationID')
                                ->innerJoin('SummitEvent', 'SummitEvent.ID = Presentation.ID')
                                ->innerJoin('Presentation_Speakers',
                                    'Presentation_Speakers.PresentationID = Presentation.ID')
                                ->innerJoin('PresentationSpeaker',
                                    'PresentationSpeaker.ID = Presentation_Speakers.PresentationSpeakerID')
                                ->leftJoin('PresentationCategory', 'PresentationCategory.ID = Presentation.CategoryID');

                            $search = trim($params['search']);
                            $parts = preg_split('/\s+/', $params['search']);

                            // sniff out speaker first/last name search
                            if (sizeof($parts) === 2) {
                                $speakerVideos = $videos->filter([
                                    'PresentationSpeaker.FirstName:PartialMatch' => $parts[0],
                                    'PresentationSpeaker.LastName:PartialMatch' => $parts[1],
                                ]);
                            } else {
                                $speakerVideos = $videos->filterAny([
                                    'PresentationSpeaker.FirstName:PartialMatch' => $search,
                                    'PresentationSpeaker.LastName:PartialMatch' => $search
                                ]);
                            }


                            $titleVideos = $videos->filter([
                                'Presentation.Title:PartialMatch' => $search
                            ])
                                ->limit($defaultLimit)
                                ->sort('DateUploaded DESC');
                            $topicVideos = $videos->filter([
                                'PresentationCategory.Title:PartialMatch' => $search
                            ])
                                ->limit($defaultLimit)
                                ->sort('DateUploaded DESC');

                            $response = [
                                'results' => [
                                    'titleMatches' => [],
                                    'speakerMatches' => [],
                                    'topicMatches' => []
                                ]
                            ];

                            foreach ($titleVideos as $v) {
                                $response['results']['titleMatches'][] = $this->createVideoJSON($v);
                            }
                            foreach ($speakerVideos as $v) {
                                $response['results']['speakerMatches'][] = $this->createVideoJSON($v);
                            }
                            foreach ($topicVideos as $v) {
                                $response['results']['topicMatches'][] = $this->createVideoJSON($v);
                            }

                            return $response;
                        }
                    }
                }
            }
        }

        $total = $videos->count();
        $limit = isset($params['popular']) ?
            SummitVideoApp::config()->popular_video_limit :
            $defaultLimit;


        $videos = $videos->limit($limit, $start);
        $hasMore = $total > ($start + $videos->count());

        $response = [
            'summit' => $summit ? $this->createSummitJSON($summit) : null,
            'speaker' => $speaker ? $this->createSpeakerJSON($speaker) : null,
            'has_more' => $hasMore,
            'total' => $total,
            'results' => []
        ];

        foreach ($videos as $v) {
            $response['results'][] = $this->createVideoJSON($v);
        }

        return $response;
    }


    /**
     * @return array|null
     */
    public function getFeaturedVideo()
    {
        $video = PresentationVideo::get()
            ->filter([
                'Featured' => true,
                'DisplayOnSite' => true,
                'Processed' => true
            ])
            ->first();

        return $video ? $this->createVideoJSON($video) : null;
    }


    /**
     * @return array|null
     */
    public function getLatestVideo()
    {
        $video = PresentationVideo::get()
            ->filter([
                'DisplayOnSite' => true,
                'Processed' => true
            ])
            ->sort('DateUploaded DESC')
            ->first();

        return $video ? $this->createVideoJSON($video) : null;
    }


    /**
     * @param array $params
     * @return array
     */
    public function getSpeakers($params = [])
    {
        $start = isset($params['start']) ? $params['start'] : 0;
        $speakers = PresentationSpeaker::get()
            ->innerJoin('Presentation_Speakers', 'Presentation_Speakers.PresentationSpeakerID = PresentationSpeaker.ID')
            ->innerJoin('Presentation', 'Presentation.ID = Presentation_Speakers.PresentationID')
            ->innerJoin('PresentationMaterial', 'PresentationMaterial.PresentationID = Presentation.ID')
            ->innerJoin('PresentationVideo', 'PresentationVideo.ID = PresentationMaterial.ID')
            ->sort('COUNT(Presentation_Speakers.ID) DESC')
            ->alterDataQuery(function ($query) {
                $query->groupby('PresentationSpeaker.ID');
            });
        if (isset($params['letter'])) {
            $speakers = $speakers->filter(
                'LastName:StartsWith',
                $params['letter']
            )->sort('LastName ASC');
        }

        $total = $speakers->count();
        $speakers = $speakers->limit(SummitVideoApp::config()->default_speaker_limit, $start);
        $hasMore = $total > ($start + $speakers->count());

        $response = [
            'results' => [],
            'has_more' => $hasMore,
            'total' => $total
        ];

        foreach ($speakers as $s) {
            $response['results'][] = $this->createSpeakerJSON($s);
        }

        return $response;
    }


    /**
     * @param array $params
     * @return array
     */
    public function getSummits($params = [])
    {
        $summits = Summit::get()
            ->filter('SummitBeginDate:LessThan', SS_DateTime::now()->Rfc2822())
            ->sort('SummitBeginDate DESC');

        $response = [
            'results' => []
        ];

        foreach ($summits as $s) {
            $response['results'][] = $this->createSummitJSON($s);
        }

        return $response;
    }


    /**
     * @param $id
     * @return array
     */
    public function getVideoDetail($id)
    {
        $video = PresentationVideo::get()->filter([
            'Presentation.Slug' => $id,
            'DisplayOnSite' => true,
            'Processed' => true
        ])->first();

        if (!$video) {
            $video = PresentationVideo::get()
                ->filter([
                    'ID' => $id,
                    'DisplayOnSite' => true,
                    'Processed' => true
                ])->first();
        }

        if ($video) {
            $cutoff = time() - SummitVideoApp::config()->video_view_staleness;
            $videoStaleness = strtotime($video->ViewsLastUpdated);
            // Refresh the views if they're not of acceptable staleness
            if (!$video->ViewsLastUpdated || $videoStaleness < $cutoff) {
                // Set the last updated regardless of the outcome, so we don't get
                // unthrottled failures.
                $video->ViewsLastUpdated = SS_DateTime::now()->Rfc2822();

                try {
                    $response = $this->youTube->getVideoStatsById($video->YouTubeID);
                    if ($response) {
                        $data = Convert::json2array($response->getBody()->getContents());
                        if (!empty($data['items'])) {
                            $videoData = $data['items'][0];
                            $video->Views = $videoData['statistics']['viewCount'];
                        }
                    }

                } catch (Exception $e) {
                    SS_Log::log("Summit video app tried to get video {$video->YouTubeID}: {$e->getMessage()}",
                        SS_Log::ERR);
                }

                $video->write();
            }

            $json = $this->createVideoJSON($video);
            $json['description'] = $video->Presentation()->ShortDescription ?: $video->Presentation()->Description;

            return $json;
        }
    }


    /**
     * @param PresentationVideo $v
     * @return array
     */
    protected function createVideoJSON(PresentationVideo $v)
    {
        $speakers = array_map(function ($s) {
            return [
                'id' => $s->ID,
                'name' => $s->getName()
            ];
        }, $v->Presentation()->Speakers()->toArray());

        $slide = $v->Presentation()->MaterialType('PresentationSlide');

        return [
            'id' => $v->ID,
            'title' => $v->Name,
            'date' => $v->Presentation()->Summit()->convertDateFromUTC2TimeZone($v->DateUploaded, 'Y-m-d'),
            'dateUTC' => $v->DateUploaded,
            'thumbnailURL' => "//img.youtube.com/vi/{$v->YouTubeID}/mqdefault.jpg",
            'summit' => [
                'id' => $v->Presentation()->SummitID,
                'title' => $v->Presentation()->Summit()->Title,
                'slug' => $v->Presentation()->Summit()->Slug
            ],
            'views' => $v->Views,
            'youtubeID' => $v->YouTubeID,
            'speakers' => $speakers,
            'slides' => $slide ? $slide->getSlideURL() : null,
            'slug' => $v->Presentation()->Slug ?: $v->ID
        ];
    }


    /**
     * @param Summit $s
     * @return array
     */
    protected function createSummitJSON(Summit $s)
    {
        $page = SummitPage::get()->filter('SummitID', $s->ID)->first();
        $image = null;
        if ($page) {
            $image = $page->SummitImage()->Image();
        }

        return [
            'id' => $s->ID,
            'title' => $s->Title,
            'dates' => $s->getSummitDateRange(),
            'videoCount' => PresentationVideo::get()->filter([
                'DisplayOnSite' => true,
                'Processed' => true,
                'PresentationID' => $s->Presentations()->column('ID')
            ])->count(),
            'imageURL' => ($image && $image->exists() && Director::fileExists($image->Filename)) ?
                $image->CroppedImage(263, 148)->URL :
                'summit-video-app/production/images/placeholder-image.jpg',
            'slug' => $s->Slug
        ];
    }


    /**
     * @param PresentationSpeaker $s
     * @return array
     */
    protected function createSpeakerJSON(PresentationSpeaker $s)
    {
        return [
            'id' => $s->ID,
            'name' => $s->getName(),
            'jobTitle' => $s->Title,
            'imageURL' => ($s->Photo()->exists() && Director::fileExists($s->Photo()->Filename)) ?
                $s->Photo()->CroppedImage(263, 148)->URL :
                'summit-video-app/production/images/placeholder-image.jpg',
            'videoCount' => $s->Presentations()
                ->innerJoin('PresentationMaterial', 'PresentationMaterial.PresentationID = Presentation.ID')
                ->innerJoin('PresentationVideo', 'PresentationVideo.ID = PresentationMaterial.ID')
                ->count()
        ];
    }

}