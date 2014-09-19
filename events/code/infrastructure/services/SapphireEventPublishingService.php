<?php
/**
 * Class SapphireEventPublishingService
 */
final class SapphireEventPublishingService implements IEventPublishingService {

	/**
	 * @param IEvent $event
	 * @throws NotFoundEntityException
	 */
	public function publish(IEvent $event)
	{
		$parent = EventHolder::get()->first();
		if(!$parent) throw new NotFoundEntityException('EventHolder','');
		$event->setParent($parent); // Should set the ID once the Holder is created...
		$event->write();
		//$event->doPublish();
	}
}