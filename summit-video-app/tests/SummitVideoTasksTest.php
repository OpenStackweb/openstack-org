<?php

require(__DIR__ . '/_data.php');
require_once(__DIR__ . '/MockYouTubeServiceGenerator.php');

/**
 * Class SummitVideoTasksTest
 */
class SummitVideoTasksTest extends SapphireTest
{
    use MockYouTubeServiceGenerator;

    /**
     * @var string
     */
    protected static $fixture_file = 'SummitVideoAppTest.yml';

    /**
     * @var array
     */
    protected $extraDataObjects = [
        'PresentationVideo'
    ];

    /**
     * Sets up the test
     */
    public function setUp()
    {
        Config::inst()->update('DataObject', 'validation_enabled', false);
        Config::inst()->update('SummitVideoApp', 'popular_video_limit', 10);

        parent::setUp();
    }


    /**
     * Tests if the task to sync views to YouTube is working
     */
    public function testSummitVideoTaskUpdatesVideoViews()
    {
        global $searchResponse;
        global $videoResponse;

        $mockService = $this->createMockYouTubeService(
            [
                'getPopularVideos' => function ($self) {
                    return $self->exactly(2);
                },
                'getVideoStatsByID' => function ($self) {
                    return $self->exactly(2);
                }
            ],
            $this->onConsecutiveCalls(
                Convert::array2json($searchResponse[0]),
                Convert::array2json($videoResponse[0]),
                Convert::array2json($searchResponse[1]),
                Convert::array2json($videoResponse[1])
            )
        );

        $mockService
            ->expects($this->at(0))
            ->method('getPopularVideos')
            ->with(
                $this->equalTo(null)
            );

        $mockService
            ->expects($this->at(2))
            ->method('getPopularVideos')
            ->with(
                $this->equalTo('NEXT_PAGE_TOKEN')
            );

        $mockService
            ->expects($this->at(1))
            ->method('getVideoStatsByID')
            ->with(
                $this->logicalAnd(
                    $this->contains(1),
                    $this->contains(2),
                    $this->contains(3),
                    $this->contains(4),
                    $this->contains(5)
                )
            );

        $mockService
            ->expects($this->at(3))
            ->method('getVideoStatsByID')
            ->with(
                $this->logicalAnd(
                    $this->contains(123),
                    $this->contains(10),
                    $this->contains(11),
                    $this->contains(12)
                )
            );

        $task = new SummitVideoViewTask($mockService);
        $task->run();

        foreach (range(1, 10) as $videoID) {
            $video = PresentationVideo::get()->filter('YouTubeID', $videoID)->first();
            $this->assertEquals($videoID * 100, $video->Views);
        }

        $this->assertEquals(12, $task->getVideosUpdated());
    }


    /**
     * Tests if the task cannot go into an infinite loop
     */
    public function testSummitVideoTaskGoesInsane()
    {
        $this->setExpectedException(Exception::class);
        global $searchResponse;
        global $videoResponse;

        $badSearchResponse = $searchResponse[0];
        foreach ($badResponse['items'] as $item) {
            $item['id']['videoId'] = 'nothing';
        }
        $badVideoResponse = $videoResponse[0];
        foreach ($badVideoResponse['items'] as $item) {
            $item['id'] = 'nothing';
        }

        $mockService = $this->createMockYouTubeService(
            ['getPopularVideos', 'getVideoStatsByID'],
            $this->onConsecutiveCalls(
                Convert::array2json($badSearchResponse),
                Convert::array2json($badVideoResponse),
                Convert::array2json($badSearchResponse),
                Convert::array2json($badVideoResponse),
                Convert::array2json($badSearchResponse),
                Convert::array2json($badVideoResponse),
                Convert::array2json($badSearchResponse),
                Convert::array2json($badVideoResponse),
                Convert::array2json($badSearchResponse),
                Convert::array2json($badVideoResponse),
                Convert::array2json($badSearchResponse)
            )
        );

        $task = new SummitVideoViewTask($mockService);
        $exception = null;
        try {
            $task->run();
        } catch (Exception $e) {
            $exception = $e;
        }

        $this->assertEquals(0, $task->getVideosUpdated());
        $this->assertInstanceOf('Exception', $e);
    }


    /**
     * Tests if when everything is marked processed, nothing happens
     */
    public function testVideoProcessingTaskWontDoAnythingIfEverythingIsProcessed()
    {
        $mockService = $this->createMockYouTubeService(
            'getVideoStatusByID'
        );

        $task = new SummitVideoProcessingTask($mockService);
        $task->run();

        $this->assertEquals(0, $task->getVideosUpdated());
    }


    /**
     * Tests if videos can be too old to get checked
     */
    public function testOldVideosAreSkippedOver()
    {
        DB::query('UPDATE PresentationVideo SET Processed=0');
        DB::query("UPDATE PresentationVideo SET DateUploaded = '2012-01-01 00:00:00' WHERE YouTubeID <= 6");
        DB::query('UPDATE PresentationVideo SET DateUploaded = DATE(NOW()) WHERE YouTubeID > 6');
        DB::query("UPDATE Summit SET Active = 1, Timezone = 279 WHERE Title = 'Tokyo'");

        $mockService = $this->createMockYouTubeService(
            'getVideoStatusByID'
        );

        $mockService
            ->expects($this->once())
            ->method('getVideoStatusByID')
            ->with($this->logicalNot(
                $this->contains(1),
                $this->contains(2),
                $this->contains(3),
                $this->contains(4),
                $this->contains(5),
                $this->contains(6)
            ));

        $task = new SummitVideoProcessingTask($mockService);
        $task->run();

        $this->assertEquals(0, $task->getVideosUpdated());
    }

    /**
     * Tests if only recently uploaded videos get the processing check
     */
    public function testItUpdatesRecentUnprocessedVideos()
    {
        global $statusResponse;

        DB::query('UPDATE PresentationVideo SET Processed=0 WHERE YouTubeID%2 = 0');
        DB::query('UPDATE PresentationVideo SET DateUploaded = DATE(NOW())');
        DB::query("UPDATE Summit SET Active = 1, Timezone = 279 WHERE Title = 'Tokyo'");

        $mockService = $this->createMockYouTubeService(
            'getVideoStatusByID',
            $this->returnValue(
                Convert::array2json($statusResponse)
            )
        );

        $mockService
            ->expects($this->once())
            ->method('getVideoStatusByID')
            ->with($this->logicalAnd(
                $this->contains(2),
                $this->contains(4),
                $this->contains(6),
                $this->contains(8),
                $this->contains(10),
                $this->contains(12)
            ));

        $task = new SummitVideoProcessingTask($mockService);
        $task->run();

        foreach (PresentationVideo::get()->filter([
            'YouTubeID' => [2, 6, 10]
        ]) as $video) {
            $this->assertEquals(1, $video->Processed);
        }

        foreach (PresentationVideo::get()->filter([
            'YouTubeID' => [4, 8, 12]
        ]) as $video) {
            $this->assertEquals(0, $video->Processed);
        }

        $this->assertEquals(3, $task->getVideosUpdated());
    }

}