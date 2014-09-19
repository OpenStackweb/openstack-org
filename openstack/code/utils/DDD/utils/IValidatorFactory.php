<?php
/**
 * Interface IValidatorFactory
 */
interface IValidatorFactory {

	/**
	 * @param array $data
	 * @return IValidator
	 */
	public function buildValidatorForCompanyService(array $data);

	/**
	 * @param array $data
	 * @return IValidator
	 */
	public function buildValidatorForCompanyResource(array $data);

	/**
	 * @param array $data
	 * @return IValidator
	 */
	public function buildValidatorForMarketPlaceVideo(array $data);

	/**
	 * @param array $data
	 * @return IValidator
	 */
	public function buildValidatorForCapability(array $data);

	/**
	 * @param array $data
	 * @return IValidator
	 */
	public function buildValidatorForServiceOffered(array $data);

	/**
	 * @param array $data
	 * @return IValidator
	 */
	public function buildValidatorForDataCenterRegion(array $data);

	/**
	 * @param array $data
	 * @return IValidator
	 */
	public function buildValidatorForDataCenterLocation(array $data);

	/**
	 * @param array $data
	 * @return IValidator
	 */
	public function buildValidatorForOffice(array $data);
} 