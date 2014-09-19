<?php
/**
 * Class SecurityGroupDecorator
 */
class SecurityGroupDecorator extends  DataExtension implements ISecurityGroup {

	public function getIdentifier()
	{
		return (int)$this->owner->getField('ID');
	}

	public function getTitle()
	{
		return $this->owner->getField('title');
	}

	public function setTitle($title)
	{
		$this->owner->setField('Title',$title);
		$this->setSlug(str_replace(' ', '-', strtolower($title)));
	}

	public function getSlug()
	{
		return $this->owner->getField('Code');
	}

	public function setSlug($slug)
	{
		$this->owner->setField('Code',$slug);
	}

	public function getDescription()
	{
		return $this->owner->getField('Description');
	}

	public function setDescription($description)
	{
		$this->owner->setField('Description',$description);
	}
}