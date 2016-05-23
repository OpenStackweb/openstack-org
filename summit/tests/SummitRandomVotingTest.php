<?php

class SummitRandomVotingTest extends SapphireTest {

	protected static $fixture_file = 'SummitTest.yml';

	public function setUp () {
		Config::inst()->update('DataObject','validation_enabled', false);
		Config::inst()->update('Summit', 'random_voting_list_count', 100);

		parent::setUp();
	}

	public function testSummitGetsActive () {
		$summit = Summit::get_active();
		$activeSummit = $this->objFromFixture('Summit', 'summit2');
		$this->assertEquals (
			$summit->ID,
			$activeSummit->ID
		);
	}

	public function testActiveSummitGeneratesPriorities() {
		$summit = Summit::get_active();
		$summit->generateVotingLists();

		$this->assertEquals (
			$summit->config()->random_voting_list_count,
			$summit->RandomVotingLists()->count()
		);

		$this->assertCount (
			(int) $summit->VoteablePresentations()->count(),
			$summit->RandomVotingLists()->first()->getPriorityList()
		);

		$this->assertCount (
			0,
			array_diff(
				$summit->RandomVotingLists()->first()->getPriorityList(),
				$summit->VoteablePresentations()->column('ID')
			)
		);

		$this->assertCount (
			0,
			array_diff(
				$summit->VoteablePresentations()->column('ID'),
				$summit->RandomVotingLists()->first()->getPriorityList()
			)
		);
	}

	public function testMemberGetsRandomPresentations () {
		$member = $this->objFromFixture('Member', 'unclecheese');
		$summit = $this->objFromFixture('Summit','summit2');
		$this->assertFalse($member->VotingList()->exists());

		$presentations = $member->getRandomisedPresentations();
		$this->assertFalse($presentations);

		$summit->generateVotingLists();	
		$presentations = $member->getRandomisedPresentations(null, $summit);
		$this->assertTrue($member->VotingList()->exists());

		$this->assertEquals(
			implode('-',$member->VotingList()->getPriorityList()),
			implode('-',$presentations->column('ID'))
		);
		// Check idempotency
		$summit->flushCache();
		$summit->generateVotingLists();	
		$presentations = $member->getRandomisedPresentations(null, $summit);
		$this->assertEquals(
			implode('-',$member->VotingList()->getPriorityList()),
			implode('-',$presentations->column('ID'))
		);

	}


//	public function testVotingControllerInitialisesPresentationPriorityWhenNeeded () {
//		$count = Summit::config()->random_voting_list_count;
//		$controller = ModelAsController::controller_for(
//			$this->objFromFixture('PresentationVotingPage','voting')
//		);
//
//		$this->assertEquals(0, Summit::get_active()->RandomVotingLists()->count());
//		$controller->init();
//
//		$this->assertEquals(
//			$count,
//			Summit::get_active()->RandomVotingLists()->count()
//		);
//	}

	public function testVotingControllerWillNotInitialisesPresentationPriorityWhenNotNeeded () {
		$s = Summit::get_active();
		$s->generateVotingLists();
		$old_ids = $s->RandomVotingLists()->column('ID');
		$s->flushCache();
		
		$controller = ModelAsController::controller_for(
			$this->objFromFixture('PresentationVotingPage','voting')
		);
		
		$controller->init();
		$new_ids = $s->RandomVotingLists()->column('ID');
		
		$this->assertEquals(
			implode('-',$old_ids),
			implode('-',$new_ids)
		);
	}

	public function testRandomisationsWillNeverExceedConfiguredLimit () {
		$s = Summit::get_active();
		$this->assertEquals(0, $s->RandomVotingLists()->count());
		$s->generateVotingLists();
		$this->assertEquals(
			$s->config()->random_voting_list_count, 
			$s->RandomVotingLists()->count()
		);
		$s->flushCache();
		$s->generateVotingLists();
		$this->assertEquals(
			$s->config()->random_voting_list_count, 
			$s->RandomVotingLists()->count()
		);		
	}

	public function testSummitVotingListIdempotence () {
		$s = Summit::get_active();
		$s->generateVotingLists();
		$s->generateVotingLists();
		$this->assertEquals(
			$s->config()->random_voting_list_count,
			PresentationRandomVotingList::get()->count()
		);
		$member = $this->objFromFixture('Member','unclecheese');
		$member->getRandomisedPresentations(null, $s);
		$this->assertTrue($member->VotingList()->exists());
		
		$oldID = $member->VotingListID;
		$s->generateVotingLists();
		$member->getRandomisedPresentations(null, $s);
		$this->assertNotEquals($oldID, $member->VotingListID);
	}	
}