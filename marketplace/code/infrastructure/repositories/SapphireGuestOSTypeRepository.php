<?php
/**
 * Class SapphireGuestOSTypeRepository
 */
class SapphireGuestOSTypeRepository
extends SapphireRepository
implements IEntityRepository
{
	public function __construct(){
		parent::__construct(new GuestOSType);
	}
} 