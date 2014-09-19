<?php

/**
 * Class SapphireJobPublishingService
 */
final class SapphireJobPublishingService
implements IJobPublishingService
{
	/**
	 * @param IJob $job
	 * @throws NotFoundEntityException
	 */
	public function publish(IJob $job){
		$parent = JobHolder::get()->first();
		if(!$parent) throw new NotFoundEntityException('JobHolder','');
		$job->setParent($parent); // Should set the ID once the Holder is created...
		$job->write();
		//$job->doPublish();
	}
} 