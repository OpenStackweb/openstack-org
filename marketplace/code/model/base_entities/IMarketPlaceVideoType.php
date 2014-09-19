<?php

/**
 * Interface IMarketPlaceVideoType
 */
interface IMarketPlaceVideoType extends IEntity {

	public function getType();
	public function setType($type);

	public function getMaxTotalTime();
	public function setMaxTotalTime($time);

} 