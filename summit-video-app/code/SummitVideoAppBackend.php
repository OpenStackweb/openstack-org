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
        foreach($params as $key => $val){
            $params[$key] = Convert::raw2sql($val);
        }

        $summit = $speaker = $tag = $track = null;
        $start = isset($params['start']) ? $params['start'] : 0;
        $defaultLimit = SummitVideoApp::config()->default_video_limit;
        $group = isset($params['group']) ? $params['group'] : 'all';
        $criteria =  ( isset($params['id']) ) ? $params['id'] : 0;

        $videos = PresentationVideo::get()
            ->filter([
                'DisplayOnSite' => true,
                'Processed' => true
            ])
            ->sort('DateUploaded', 'DESC');

        switch ($group) {
            case 'summit':

                // legacy urls like /videos/summits/show/6
                if (is_numeric($criteria)) {
                    $summit = Summit::get()->byID($criteria);
                } else {
                    $summit = Summit::get()->filter('Slug', $criteria)->first();
                }

                if ($summit) {
                    $videos = $videos
                        ->innerJoin('SummitEvent', 'SummitEvent.ID = PresentationMaterial.PresentationID')
                        ->filter('SummitEvent.SummitID', $summit->ID);
                } else {
                    $videos = ArrayList::create();
                }
                break;

            case 'tag':
                $tag = Tag::get()->filter('Tag', $criteria)->first();
                if ($tag) {
                    $videos = $videos
                        ->innerJoin('SummitEvent_Tags', 'SummitEvent_Tags.SummitEventID = PresentationMaterial.PresentationID')
                        ->filter('SummitEvent_Tags.TagID', $tag->ID);
                } else {
                    $videos = ArrayList::create();
                }
                break;

            case 'track':
                $summit =  ( isset($params['summit']) ) ? $params['summit'] : 0;
                $summit = Summit::get()->filter('Slug', $summit)->first();
                $track = PresentationCategory::get()->filter(['Slug' => $criteria, 'SummitID' => $summit->ID])->first();

                if ($track) {
                    $videos = $videos
                        ->innerJoin('SummitEvent', 'SummitEvent.ID = PresentationMaterial.PresentationID')
                        ->filter('SummitEvent.CategoryID', $track->ID);
                } else {
                    $videos = ArrayList::create();
                }
                break;

            case 'speaker':
                $speaker = PresentationSpeaker::get()->byID($criteria);
                if ($speaker) {
                    $videos = $videos
                        ->innerJoin('Presentation', 'Presentation.ID = PresentationMaterial.PresentationID')
                        ->innerJoin('Presentation_Speakers', 'Presentation_Speakers.PresentationID = Presentation.ID')
                        ->filter('Presentation_Speakers.PresentationSpeakerID', $speaker->ID);
                } else {
                    $videos = ArrayList::create();
                }
                break;

            case 'popular':
                $videos = $videos->sort('Views DESC');
                break;

            case 'highlighted':
                $videos = $videos->filter(['Highlighted' => true]);
                break;

            case 'search':
                $search = trim($criteria);
                $search_words = explode(' ',$search);

                foreach ($search_words as $key => $search_word) {
                    // check for summit
                    $summit = Summit::get()->filter('Title:PartialMatch', $search_word)->first();
                    if ($summit) {
                        $videos = $videos->where('Summit.ID = '.$summit->ID);
                        unset($search_words[$key]);
                        continue;
                    }
                }

                $search = implode(' ', $search_words);
                if (!empty($search)) {
                    $videos = $videos->where("
                        CONCAT(PresentationSpeaker.FirstName,' ',PresentationSpeaker.LastName) = '$search'
                        OR PresentationSpeaker.FirstName LIKE '%$search%'
                        OR PresentationSpeaker.LastName LIKE '%$search%'
                        OR PresentationCategory.Title LIKE '%$search%'
                        OR SummitEvent.Title LIKE '%$search%'"
                    );
                }

                $videos = $videos
                    ->innerJoin('Presentation', 'Presentation.ID = PresentationMaterial.PresentationID')
                    ->innerJoin('SummitEvent', 'SummitEvent.ID = Presentation.ID')
                    ->innerJoin('Summit', 'Summit.ID = SummitEvent.SummitID')
                    ->innerJoin('Presentation_Speakers',
                        'Presentation_Speakers.PresentationID = Presentation.ID')
                    ->innerJoin('PresentationSpeaker',
                        'PresentationSpeaker.ID = Presentation_Speakers.PresentationSpeakerID')
                    ->leftJoin('PresentationCategory', 'PresentationCategory.ID = SummitEvent.CategoryID')
                    ->limit($defaultLimit);

                $search_results = $videos->toArray();
                $unique_youtube_ids = [];
                $unique_videos = [];

                foreach ($search_results as $v) {
                    if (!isset($unique_youtube_ids[$v->YouTubeID])) {
                        $unique_youtube_ids[$v->YouTubeID] = 1;
                        $unique_videos[] = $this->createVideoJSON($v);
                    }
                }

                return array( 'results' => $unique_videos );

        }

        $grouped_videos = GroupedList::create($videos)->groupBy('YouTubeID');
        $total = count($grouped_videos);
        $limit = ($group == 'popular') ?
            SummitVideoApp::config()->popular_video_limit :
            $defaultLimit;


        $videos = $videos->limit($limit, $start);
        $grouped_videos_limited = GroupedList::create($videos)->groupBy('YouTubeID');
        $hasMore = $total > ($start + count($grouped_videos_limited));

        $response = [
            'summit' => $summit ? $this->createSummitJSON($summit) : null,
            'speaker' => $speaker ? $this->createSpeakerJSON($speaker) : null,
            'tag' => $tag ? $this->createTagJSON($tag) : null,
            'track' => $track ? $this->createTrackJSON($track) : null,
            'has_more' => $hasMore,
            'total' => $total,
            'results' => []
        ];

        foreach ($grouped_videos_limited as $v) {
            if (is_a($v,'ArrayList'))
                $v = $v->first();

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
                'Featured'      => true,
                'DisplayOnSite' => true,
                'Processed'     => true
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
        foreach($params as $key => $val){
            $params[$key] = Convert::raw2sql($val);
        }

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
            $letter = $params['letter'];
            $speakers = $speakers->filter(
                'LastName:StartsWith',
                $letter
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
                        SS_Log::WARN);
                }

                $video->write();
            }

            $json = $this->createVideoJSON($video);
            $json['description'] = $video->Presentation()->Abstract ?: $video->Presentation()->Abstract;

            return $json;
        }
    }


    /**
     * @param PresentationVideo $v
     * @return array
     */
    protected function createVideoJSON(PresentationVideo $v)
    {
        $moderator = null;
        $m = $v->Presentation()->Moderator();
        if ($m->Exists()) {
            $moderator = [
                'id' => $m->ID,
                'name' => $m->getName(),
                'name_slug' => $m->getNameSlug()
            ];
        }

        $speakers = array_map(function ($s) {
            return [
                'id' => $s->ID,
                'name' => $s->getName(),
                'name_slug' => $s->getNameSlug(),
            ];
        }, $v->Presentation()->Speakers()->toArray());

        $tags = array_map(function ($t) {
            return [
                'id' => $t->ID,
                'tag' => $t->Tag
            ];
        }, $v->Presentation()->Tags()->toArray());

        $track_obj = $v->Presentation()->Category();
        $track = ['id' => $track_obj->ID, 'title' => $track_obj->Title, 'slug' => $track_obj->Slug];

        $slide = $v->Presentation()->MaterialType('PresentationSlide');
        $links = [];

        $raw_links = $v->Presentation()->getMaterialByType('PresentationLink');

        if ($raw_links) {
            foreach( $raw_links as $link){
                $links[] = [
                    'url'   => $link->Link,
                    'title' => $link->Name
                ];
            }
        }

        $has_presentation = $v->Presentation()->Exists();
        $summit_array = [];

        if ($has_presentation) {
            $summit_array = [
                'id' => $v->Presentation()->SummitID,
                'title' => $v->Presentation()->Summit()->Title,
                'slug' => $v->Presentation()->Summit()->Slug
            ];
        }

        $video_date = date('Y-m-d', strtotime($v->DateUploaded));
        if ($has_presentation) {
            $video_date = $v->Presentation()->Summit()->convertDateFromUTC2TimeZone($v->DateUploaded, 'Y-m-d');
        }

        return [
            'id'            => $v->ID,
            'title'         => $v->Name,
            'date'          => $video_date,
            'dateUTC'       => $v->DateUploaded,
            'thumbnailURL'  => "//img.youtube.com/vi/{$v->YouTubeID}/mqdefault.jpg",
            'summit'        => $summit_array,
            'views'         => $v->Views,
            'youtubeID'     => $v->YouTubeID,
            'speakers'      => $speakers,
            'moderator'     => $moderator,
            'slides'        => $slide ? $slide->getSlideURL() : null,
            'slug'          => $has_presentation ? $v->Presentation()->Slug : $v->ID,
            'tags'          => $tags,
            'track'         => $track,
            'links'         => $links,
        ];
    }

    private function getSummitImage(Summit $s) {
        $image = null;

        if ($page = SummitPage::get()->filter('SummitID', $s->ID)->first()) {
            $image = $page->SummitImage()->Image();
        } else if ($page = ConferencePage::get()->filter('SummitID', $s->ID)->first()) {
            $image = $page->SummitImage();
        }

        $default_image_url = 'summit-video-app/ui/production/images/placeholder-image.jpg';
        $summit_image_url = 'summit-video-app/ui/production/images/summit-'.$s->ID.'.jpg';

        if (Director::fileExists($summit_image_url)) {
            return $summit_image_url;
        } else if($image && $image->exists() && Director::fileExists($image->Filename)) {
            return $image->CroppedImage(263, 148)->URL;
        } else {
            return $default_image_url;
        }

    }

    /**
     * @param Summit $s
     * @return array
     */
    protected function createSummitJSON(Summit $s)
    {
        $tracks = array_map(function ($t) {
            return [
                'id'         => $t->ID,
                'slug'       => $t->Slug,
                'title'      => $t->Title,
                'has_videos' => 0
            ];
        }, $s->Categories()->toArray());

        $videos = PresentationVideo::get()
            ->filter([
                'DisplayOnSite' => true,
                'Processed' => true
            ])
            ->sort('DateUploaded', 'DESC')
            ->innerJoin('Presentation', 'Presentation.ID = PresentationMaterial.PresentationID')
            ->innerJoin('SummitEvent', 'SummitEvent.ID = PresentationMaterial.PresentationID')
            ->filter('SummitEvent.SummitID', $s->ID);

        $videos_groupedby_track = GroupedList::create($videos)->groupBy('Track');

        foreach ($tracks as &$track) {
            if (array_key_exists($track['title'], $videos_groupedby_track)) {
                $track['has_videos'] = 1;
            }
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
            'imageURL' => $this->getSummitImage($s),
            'slug' => $s->Slug,
            'tracks' => $tracks
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
            'slug' => singleton('SiteTree')->generateURLSegment($s->getName()),
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

    /**
     * @param Tag $t
     * @return array
     */
    protected function createTagJSON(Tag $t)
    {
        return [
            'id'  => $t->ID,
            'tag' => $t->Tag
        ];
    }

    /**
     * @param PresentationCategory $track
     * @return array
     */
    protected function createTrackJSON(PresentationCategory $track)
    {
        return [
            'id'    => $track->ID,
            'title' => $track->Title,
            'slug'  => $track->Slug
        ];
    }

}