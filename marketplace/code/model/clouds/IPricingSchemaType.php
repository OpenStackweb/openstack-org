<?php
/**
 * Interface IPricingSchemaType
 */
interface IPricingSchemaType
	extends IEntity {
	public function setType($type);
	public function getType();
} 