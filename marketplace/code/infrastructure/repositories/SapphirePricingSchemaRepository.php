<?php

/**
 * Class SapphirePricingSchemaRepository
 */
class SapphirePricingSchemaRepository
	extends SapphireRepository
	implements IEntityRepository
{
	public function __construct(){
		parent::__construct(new PricingSchemaType);
	}
}