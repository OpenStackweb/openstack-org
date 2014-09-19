<?php
/**
 * Interface IConsultantFactory
 */
interface IConsultantFactory
	extends IRegionalSupportedCompanyServiceFactory {
	/**
	 * @param string $name
	 * @return ISpokenLanguage
	 */
	public function buildSpokenLanguage($name);

	/**
	 * @param string $type
	 * @return IConfigurationManagementType
	 */
	public function buildConfigurationManagementType($type);

	/**
	 * @param string $name
	 * @return IConsultantClient
	 */
	public function buildClient($name);

	public function buildOffice(AddressInfo $address_info);

} 