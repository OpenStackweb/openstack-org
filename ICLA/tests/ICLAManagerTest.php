<?php

/**
 * Class ICLAManagerTest
 */
final class ICLAManagerTest extends SapphireTest {

	/**
	 * @expectedException UnauthorizedRestfullAPIException
	 */
	public function testprocessICLAGroupUnauthorizedRestfullAPIException(){

		$manager = new ICLAManager (
			new GerritAPI('https://review.openstack.org', 'smarcet', ''),
			new SapphireBatchTaskRepository,
			new SapphireCLAMemberRepository,
			new BatchTaskFactory,
			SapphireTransactionManager::getInstance()
		);

		$manager->processICLAGroup('a49e4febb69477d0aa5737038c1802dd6cab67c5',10);

		$this->setExpectedException('UnauthorizedRestfullAPIException');
	}


	public function testprocessICLAGroup(){

		$manager = new ICLAManager (
			new GerritAPI('https://review.openstack.org', 'smarcet', 'TwxKcgZurLX6'),
			new SapphireBatchTaskRepository,
			new SapphireCLAMemberRepository,
			new BatchTaskFactory,
			SapphireTransactionManager::getInstance()
		);

		$manager->processICLAGroup('a49e4febb69477d0aa5737038c1802dd6cab67c5',10);

	}
} 