<?php
/**
 * Class SapphireHyperVisorTypeRepository
 */
class SapphireHyperVisorTypeRepository
extends SapphireRepository
implements IEntityRepository
{
	public function __construct(){
		parent::__construct(new HyperVisorType);
	}
} 