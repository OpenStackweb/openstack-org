<?php

/**
 * Class SummitVideoYouTubeService
 */
class SummitVideoYouTubeService
{
    /**
     * @var
     */
    protected $http;

    /**
     * SummitVideoYouTubeService constructor.
     * @param $http
     */
    public function __construct($http)
    {
        if (!defined('OPENSTACK_YOUTUBE_API_KEY')) {
            throw new RuntimeException('You must define the environment variable OPENSTACK_YOUTUBE_API_KEY to use this service');
        }
        if (!defined('OPENSTACK_YOUTUBE_CHANNEL_ID')) {
            throw new RuntimeException('You must define the environment variable OPENSTACK_YOUTUBE_CHANNEL_ID to use this service');
        }

        $this->http = $http;
    }

    /**
     * @param null $pageToken
     * @return mixed
     */
    public function getPopularVideos($pageToken = null)
    {
        return $this->get('search', [
            'order' => 'viewCount',
            'maxResults' => 50,
            'part' => 'snippet',
            'pageToken' => $pageToken
        ]);
    }


    /**
     * @param array $ids
     * @return mixed
     */
    public function getVideoStatsById($ids = [])
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        return $this->get('videos', [
            'id' => implode(',', $ids),
            'part' => 'statistics'
        ]);
    }


    /**
     * @param array $ids
     * @return mixed
     */
    public function getVideoStatusById($ids = [])
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        return $this->get('videos', [
            'id' => implode(',', $ids),
            'part' => 'id, status'
        ]);
    }


    /**
     * @param $endpoint
     * @param array $queryParams
     * @return mixed
     */
    protected function get($endpoint, $queryParams = [])
    {
        $defaultParams = [
            'key' => OPENSTACK_YOUTUBE_API_KEY,
            'channelId' => OPENSTACK_YOUTUBE_CHANNEL_ID
        ];

        return $this->http->get($endpoint, [
            'query' => array_merge($defaultParams, $queryParams)
        ]);
    }

}