<?php

/**
 * Interface INewsPublishingService
 */
interface INewsPublishingService {
	/**
	 * @param INews $news
	 * @throws NotFoundEntityException
	 */
	public function publish(INews $news);
} 