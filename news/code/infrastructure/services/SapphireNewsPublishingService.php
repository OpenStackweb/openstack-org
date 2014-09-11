<?php

/**
 * Class SapphireNewsPublishingService
 */
final class SapphireNewsPublishingService
implements INewsPublishingService
{
	/**
	 * @param INews $news
	 * @throws NotFoundEntityException
	 */
	public function publish(INews $news){
		$parent = DataObject::get_one("NewsHolder",null);
		if(!$parent) throw new NotFoundEntityException('NewsHolder','');
		$news->setParent($parent); // Should set the ID once the Holder is created...
        $news->write();
        $news->publish("Stage","Live");
	}
} 