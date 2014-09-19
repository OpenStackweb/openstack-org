<?php

/**
 * Interface ICloudFactory
 */
interface ICloudFactory extends IOpenStackImplementationFactory {
	/**
	 * @param int $id
	 * @return IPricingSchemaType
	 */
	public function buildPricingSchemaById($id);
} 