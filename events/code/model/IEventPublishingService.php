<?php
/**
 * Interface IEventPublishingService
 */
interface IEventPublishingService {
	/**
	 * @param IEvent $event
	 * @throws NotFoundEntityException
	 */
	public function publish(IEvent $event);
} 