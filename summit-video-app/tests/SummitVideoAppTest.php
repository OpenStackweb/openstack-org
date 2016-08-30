<?php

require_once(__DIR__ . '/MockYouTubeServiceGenerator.php');
require_once(__DIR__ . '/_data.php');

/**
 * Class SummitVideoAppTest
 */
class SummitVideoAppTest extends SapphireTest
{

    use MockYouTubeServiceGenerator;

    /**
     * @var string
     */
    protected static $fixture_file = 'SummitVideoAppTest.yml';

    /**
     * @var
     */
    protected $page;

    /**
     * @var array
     */
    protected $extraDataObjects = [
        'PresentationVideo'
    ];

    /**
     * @param $url
     * @return mixed
     */
    protected function getURL($url)
    {
        return Director::test($this->page->Link($url));
    }

    /**
     * Set up the test
     */
    public function setUp()
    {
        Config::inst()->update('DataObject', 'validation_enabled', false);
        Config::nest();
        parent::setUp();

        $this->page = $this->objFromFixture('SummitVideoApp', 'App');
        $this->page->publish("Stage", "Live");
    }


    /**
     * Tests if videos update their upload date
     */
    public function testPresentationVideosUpdateTheirUploadDate()
    {
        $video = new PresentationVideo();
        $video->write();
        $this->assertEquals(
            date('Y-m-d'),
            $video->obj('DateUploaded')->Format('Y-m-d')
        );
    }

    /**
     * Test if the API gets a list of presentations
     */
    public function testItGetsAllPresentations()
    {
        $page = $this->objFromFixture('SummitVideoApp', 'App');
        $page->publish("Stage", "Live");
        $response = Director::test($page->Link('api/videos'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());

        $data = Convert::json2array($response->getBody());
        $this->assertNull($data['summit']);
        $this->assertNull($data['speaker']);
        $this->assertCount(12, $data['results']);
        $this->assertEquals(
            $this->objFromFixture('PresentationVideo', 'Video1')->ID,
            $data['results'][0]['id']
        );
        $this->assertEquals(
            $this->objFromFixture('PresentationVideo', 'HighlightVideo1')->ID,
            $data['results'][3]['id']
        );

    }


    /**
     * Tests if unprocessed videos are hidden
     */
    public function testItHidesUnprocessedVideos()
    {
        DB::query("UPDATE PresentationVideo SET Processed = 0 WHERE YouTubeID%2 = 0");
        $response = $this->getURL('api/videos');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());

        $data = Convert::json2array($response->getBody());
        $this->assertNull($data['summit']);
        $this->assertNull($data['speaker']);
        $this->assertCount(6, $data['results']);
        foreach ($data['results'] as $video) {
            $this->assertEquals(1, PresentationVideo::get()->byID($video['id'])->Processed);
        }
    }


    /**
     * Tests if only display on site videos are shown
     */
    public function testItHidesVideosThatAreNotSetToDisplayOnSite()
    {
        foreach (PresentationVideo::get() as $v) {
            if ($v->YouTubeID % 2 === 0) {
                $v->DisplayOnSite = false;
                $v->write();
            }
        }

        $response = $this->getURL('api/videos');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());

        $data = Convert::json2array($response->getBody());
        $this->assertNull($data['summit']);
        $this->assertNull($data['speaker']);
        $this->assertCount(6, $data['results']);
        foreach ($data['results'] as $video) {
            $this->assertEquals(1, PresentationVideo::get()->byID($video['id'])->DisplayOnSite);
        }
    }

    /**
     * Test if the featured video is working as expected
     */
    public function testFeaturedVideo()
    {
        $featuredVideo = $this->objFromFixture('PresentationVideo', 'FeaturedVideo');

        $response = $this->getURL('api/video/featured');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());

        $data = Convert::json2array($response->getBody());

        $this->assertEquals($featuredVideo->ID, $data['id']);

        $featuredVideo->Processed = 0;
        $featuredVideo->DisplayOnSite = 1;
        $featuredVideo->write();

        $response = $this->getURL('api/video/featured');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());

        $data = Convert::json2array($response->getBody());
        $this->assertEmpty($data);

        $featuredVideo->Processed = 1;
        $featuredVideo->DisplayOnSite = 0;
        $featuredVideo->write();

        $response = $this->getURL('api/video/featured');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());

        $data = Convert::json2array($response->getBody());
        $this->assertEmpty($data);

    }


    /**
     * Tests if the latest video is working as expected
     */
    public function testLatestVideo()
    {
        foreach (PresentationVideo::get() as $v) {
            $v->DateUploaded = '2015-01-01 00:00:00';
            $v->write();
        }

        $latestVideo = $this->objFromFixture('PresentationVideo', 'Video1');
        $latestVideo->DateUploaded = date('Y-m-d H:i:s');
        $latestVideo->write();

        $earlierVideo = $this->objFromFixture('PresentationVideo', 'Video2');
        $earlierVideo->DateUploaded = date('Y-m-d H:i:s', strtotime('-1 minute'));
        $earlierVideo->write();

        $response = $this->getURL('api/video/latest');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());

        $data = Convert::json2array($response->getBody());

        $this->assertEquals($latestVideo->ID, $data['id']);

        $latestVideo->Processed = 0;
        $latestVideo->DisplayOnSite = 1;
        $latestVideo->write();

        $response = $this->getURL('api/video/latest');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());

        $data = Convert::json2array($response->getBody());
        $this->assertEquals($earlierVideo->ID, $data['id']);

        $latestVideo->Processed = 1;
        $latestVideo->DisplayOnSite = 0;
        $latestVideo->write();

        $response = $this->getURL('api/video/latest');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());

        $data = Convert::json2array($response->getBody());
        $this->assertEquals($earlierVideo->ID, $data['id']);

    }

    /**
     * Tests if the view count is updated when video detail is fetched
     */
    public function testItUpdatesTheViewCount()
    {
        global $videoResponse;

        $video1 = $this->objFromFixture('PresentationVideo', 'Video1');
        $video1->Views = 0;
        $video1->ViewsLastUpdated = '2000-01-01 00:00:00';
        $video1->write();

        $mockService = $this->createMockYouTubeService(
            [
                'getVideoStatsByID' => function ($self) {
                    return $self->once();
                }
            ],
            $this->returnValue(
                Convert::array2json(
                    [
                        'items' => [
                            $videoResponse[0]['items'][0]
                        ]
                    ]
                )
            )
        );
        $mockService
            ->expects($this->once())
            ->method('getVideoStatsByID')
            ->with($this->equalTo($video1->YouTubeID));

        $backend = new SummitVideoAppBackend($mockService);
        $videoDetail = $backend->getVideoDetail($video1->ID);

        $video1 = PresentationVideo::get()->filter('YouTubeID', $video1->YouTubeID)->first();
        $this->assertEquals(100, $video1->Views);
    }

    /**
     * Tests if the view count updates are throttled
     */
    public function testItWontUpdateTheViewCountTooFrequently()
    {

        Config::inst()->update('SummitVideoApp', 'video_view_staleness', 3600);

        $video1 = $this->objFromFixture('PresentationVideo', 'Video1');
        $video1->Views = 0;
        $video1->ViewsLastUpdated = date('Y-m-d H:i:s', strtotime('-10 minutes'));
        $video1->write();

        $mockService = $this->createMockYouTubeService(
            [
                'getVideoStatsByID' => function ($self) {
                    return $self->never();
                }
            ]
        );

        $mockService
            ->expects($this->never())
            ->method('getVideoStatsByID');

        $backend = new SummitVideoAppBackend($mockService);
        $videoDetail = $backend->getVideoDetail($video1->ID);

        $video1 = PresentationVideo::get()->filter('YouTubeID', $video1->YouTubeID)->first();
        $this->assertEquals(0, $video1->Views);

    }

    /**
     * Tests if the video gets stamped even when the YouTube service fails
     */
    public function testItWillUpdateTheLastViewsUpdatedWhenServiceFails()
    {
        Config::inst()->update('SummitVideoApp', 'video_view_staleness', 3600);

        $video1 = $this->objFromFixture('PresentationVideo', 'Video1');
        $video1->Views = 10;
        $video1->ViewsLastUpdated = date('Y-m-d H:i:s', strtotime('-2 hours'));
        $video1->write();

        $mockService = $this->createMockYouTubeService(
            [
                'getVideoStatsByID' => function ($self) {
                    return $self->once();
                }
            ],
            $this->returnValue(
                new Exception('It failed spectacularly')
            )
        );

        $mockService
            ->expects($this->once())
            ->method('getVideoStatsByID')
            ->with($this->equalTo($video1->YouTubeID));

        $backend = new SummitVideoAppBackend($mockService);
        $videoDetail = $backend->getVideoDetail($video1->ID);

        $video1 = PresentationVideo::get()->filter('YouTubeID', $video1->YouTubeID)->first();
        $this->assertEquals(10, $video1->Views);

        // In all likelihood, the diff should be 0, here. Timestamp is set to now.
        // But for testing we'll have a bit of tolerance.
        $diff = strtotime(SS_DateTime::now()->Rfc2822()) - strtotime($video1->ViewsLastUpdated);
        $this->assertLessThan(10, $diff);

    }

}