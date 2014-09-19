<?php
/**
 * Class SapphireSupportChannelTypeRepository
 */
class SapphireSupportChannelTypeRepository
extends SapphireRepository
implements IEntityRepository
{
	public function __construct(){
		parent::__construct(new SupportChannelType);
	}
} 