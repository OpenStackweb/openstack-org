<?php
/**
 * Class ConsultantFactory
 */
final class ConsultantFactory
	extends RegionalSupportedCompanyServiceFactory
	implements IConsultantFactory {

	/**
	 * @param string           $name
	 * @param string           $overview
	 * @param ICompany         $company
	 * @param bool             $active
	 * @param IMarketPlaceType $marketplace_type
	 * @param null|string      $call_2_action_url
	 * @return ICompanyService
	 */
	public function buildCompanyService($name, $overview, ICompany $company, $active, IMarketPlaceType $marketplace_type, $call_2_action_url = null)
	{
		$consultant = new Consultant;
		$consultant->setName($name);
		$consultant->setOverview($overview);
		$consultant->setCompany($company);
		if($active)
			$consultant->activate();
		else
			$consultant->deactivate();
		$consultant->setMarketplace($marketplace_type);
		$consultant->setCall2ActionUri($call_2_action_url);
		return $consultant;
	}

	/**
	 * @param $id
	 * @return ICompanyService
	 */
	public function buildCompanyServiceById($id)
	{
		$consultant     = new Consultant;
		$consultant->ID = $id;
		return $consultant;
	}

	/**
	 * @param string $name
	 * @return ISpokenLanguage
	 */
	public function buildSpokenLanguage($name)
	{
		$language = new SpokenLanguage;
		$language->setName($name);
		return $language;
	}

	/**
	 * @param string $type
	 * @return IConfigurationManagementType
	 */
	public function buildConfigurationManagementType($type)
	{
		$config_management = new ConfigurationManagementType;
		$config_management->setType($type);
		return $config_management;
	}

	/**
	 * @param string $name
	 * @return IConsultantClient
	 */
	public function buildClient($name)
	{
		$client = new ConsultantClient;
		$client->setName($name);
		return $client;
	}

	public function buildOffice(AddressInfo $address_info)
	{
		$office = new Office;
		list($address1,$address2)=$address_info->getAddress();
		$office->setAddress($address1);
		$office->setAddress1($address2);
		$office->setZipCode($address_info->getZipCode());
		$office->setCity($address_info->getCity());
		$office->setState($address_info->getState());
		$office->setCountry($address_info->getCountry());
		return $office;
	}

}