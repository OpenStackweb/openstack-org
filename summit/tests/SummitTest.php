<?php

class SummitTest extends SapphireTest {

	protected static $fixture_file = 'SummitTest.yml';

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
		$summit->generatePresentationPriorities();

		$this->assertEquals (
			100,
			$summit->PresentationPriorities()->count()
		);

		$this->assertCount (
			(int) $summit->Presentations()->count(),
			$summit->PresentationPriorities()->first()->getPriorityList()
		);

		$this->assertCount (
			0,
			array_diff(
				$summit->PresentationPriorities()->first()->getPriorityList(),
				$summit->Presentations()->column('ID')
			)
		);

		$this->assertCount (
			0,
			array_diff(
				$summit->Presentations()->column('ID'),
				$summit->PresentationPriorities()->first()->getPriorityList()
			)
		);
	}

	public function testMemberGetsRandomPresentations () {
		$member = $this->objFromFixture('Member', 'unclecheese');
		$summit = $this->objFromFixture('Summit','summit2');
		$this->assertFalse($member->PresentationPriority()->exists());

		$presentations = $member->getRandomisedPresentations();
		$this->assertFalse($presentations);

		$summit->generatePresentationPriorities();	
		$presentations = $member->getRandomisedPresentations(null, $summit);
		$this->assertTrue($member->PresentationPriority()->exists());

		$this->assertEquals(
			implode('-',$member->PresentationPriority()->getPriorityList()),
			implode('-',$presentations->column('ID'))
		);
	}


	public function testVotingControllerInitialisesPresentationPriorityWhenNeeded () {
		$controller = ModelAsController::controller_for(
			$this->objFromFixture('PresentationVotingPage','voting')
		);

		$this->assertEquals(0, Summit::get_active()->PresentationPriorities()->count());
		$controller->init();

		$this->assertEquals(
			100,
			Summit::get_active()->PresentationPriorities()->count()
		);
	}

	public function testVotingControllerWillNotInitialisesPresentationPriorityWhenNotNeeded () {
		$s = Summit::get_active();
		$s->generatePresentationPriorities();
		$old_ids = $s->PresentationPriorities()->column('ID');
		$s->flushCache();
		
		$controller = ModelAsController::controller_for(
			$this->objFromFixture('PresentationVotingPage','voting')
		);
		
		$controller->init();
		$new_ids = $s->PresentationPriorities()->column('ID');
		
		$this->assertEquals(
			implode('-',$old_ids),
			implode('-',$new_ids)
		);
	}	
}