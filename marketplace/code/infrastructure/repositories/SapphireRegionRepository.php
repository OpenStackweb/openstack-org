<?php
/**
 * Class SapphireRegionRepository
 */
class SapphireRegionRepository
extends SapphireRepository
implements IEntityRepository
{
	public function __construct(){
		parent::__construct(new Region);
	}
} 