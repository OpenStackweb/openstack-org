<?php

/**
 * Interface IJobPublishingService
 */
interface IJobPublishingService {
	/**
	 * @param IJob $job
	 * @throws NotFoundEntityException
	 */
	public function publish(IJob $job);
} 