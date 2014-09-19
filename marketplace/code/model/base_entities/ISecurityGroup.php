<?php

/**
 * Interface ISecurityGroup
 */
interface ISecurityGroup extends IEntity  {
	public function getTitle();
	public function setTitle($title);
	public function getSlug();
	public function setSlug($slug);
	public function getDescription();
	public function setDescription($description);
} 