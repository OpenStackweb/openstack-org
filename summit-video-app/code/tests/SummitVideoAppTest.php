<?php

class SummitVideoAppTest extends SapphireTest {


	protected static $fixture_file = 'SummitVideoAppTest.yml';


	public function setUp() {
		Config::inst()->update('DataObject','validation_enabled', false);
	}


	public function testPresentationVideosUpdateTheirUploadDate() {
		$video = new PresentationVideo();
		$video->write();
		$this->assertNull($video->DateUploaded);

		$video->YouTubeID = '123';
		$video->write();
		$this->assertEquals(
			date('Y-m-d'),
			$video->obj('DateUploaded')->Format('Y-m-d')
		);
	}

	public function testItGetsAllPresentations() {
		$page = $this->objFromFixture('SummitVideoApp','App');
		$controller = new SummitVideoApp_Controller($page);
		$response = $controller->handleRequest(
			new SS_HTTPRequest(
				'GET',
				$controller->Link('api/videos')
			)
		);

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertNotEmpty($response->getBody());
		
		$data = Convert::json2array($response->getBody());

		$this->assertNull($data['summit']);
		$this->assertNull($data['speaker']);
		$this->assertCount(6, $data['results']);
		$this->assertEquals(
			$this->objFromFixture('PresentationVideo','Video1')->ID,
			$data['results'][0]['id']
		);
		$this->assertEquals(
			$this->objFromFixture('PresentationVideo','HighlightVideo1')->ID,
			end($data['results'])['id']
		);

	}
}