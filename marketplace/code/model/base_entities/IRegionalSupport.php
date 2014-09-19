<?php

/**
 * Interface IRegionalSupport
 */
interface IRegionalSupport extends IEntity {

	/**
	 * @return int
	 */
	public function getOrder();

	/**
	 * @param int $order
	 * @return void
	 */
	public function setOrder($order);

	/**
	 * @return IRegion
	 */
	public function getRegion();

	/**
	 * @param IRegion $region
	 * @return void
	 */
	public function setRegion(IRegion $region);

	/**
	 * @return IRegionalSupportedCompanyService
	 */
	public function getCompanyService();

	/**
	 * @param IRegionalSupportedCompanyService $company_service
	 * @return void
	 */
	public function setCompanyService(IRegionalSupportedCompanyService $company_service);

	/**
	 * @return ISupportChannelType[]
	 */
	public function getSupportChannelTypes();

	/**
	 * @param ISupportChannelType $channel_type
	 * @param string                    $data
	 * @return void
	 */
	public function addSupportChannelType(ISupportChannelType $channel_type, $data);

	public function clearChannelTypes();
} 