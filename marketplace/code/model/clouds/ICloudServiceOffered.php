<?php

/**
 * Interface ICloudServiceOffered
 */
interface ICloudServiceOffered
	extends IOpenStackImplementationApiCoverage {

	/**
	 * @return IPricingSchemaType[]
	 */
	public function getPricingSchemas();

	/**
	 * @param IPricingSchemaType $pricing_schema
	 * @return void
	 */
	public function addPricingSchema(IPricingSchemaType $pricing_schema);

	public function clearPricingSchemas();
} 