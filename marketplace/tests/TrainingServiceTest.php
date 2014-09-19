<?php
/**
 * Class TrainingServiceTest
 */
class TrainingServiceTest extends SapphireTest {

	/**
	 *
	 */
	public function testCreateTraining(){
		$factory  = new MarketplaceFactory;
		$training = $factory->buildTraining();
		$training->setName('test training');
		$training->setDescription('test training description');
		$training->activate();
		$service    = new TrainingServiceManager($repository = new SapphireTrainingServiceRepository());
		$repository->add($training);
		$courses    = $service->getCoursesByDate($training->getIdentifier(), DateTimeUtils::getCurrentDate());
	}
} 