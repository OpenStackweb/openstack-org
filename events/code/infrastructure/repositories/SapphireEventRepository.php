<?php

/**
 * Class SapphireEventRepository
 */
final class SapphireEventRepository extends SapphireRepository {

	public function __construct(){
		parent::__construct(new EventPage);
	}
}