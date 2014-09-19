<?php
/**
 * Interface IConsultant
 */
interface IConsultant extends IRegionalSupportedCompanyService {

	const MarketPlaceType           = 'Consultant';
	const MarketPlaceGroupSlug      = 'marketplace-consultant-administrators';
	const MarketPlacePermissionSlug = 'MANAGE_MARKETPLACE_CONSULTANT';

	//offices
	/**
	 * @return IOffice[]
	 */
	public function getOffices();
	/**
	 * @param IOffice $office
	 * @return void
	 */
	public function addOffice(IOffice $office);
	/**
	 * @return void
	 */
	public function clearOffices();
	//clients
	/**
	 * @return IConsultantClient[]
	 */
	public function getPreviousClients();
	/**
	 * @param IConsultantClient $client
	 * @return void
	 */
	public function addPreviousClients(IConsultantClient $client);
	/**
	 * @return void
	 */
	public function clearClients();
	// languages
	/**
	 * @return ISpokenLanguage[]
	 */
	public function getSpokenLanguages();

	/**
	 * @param ISpokenLanguage $language
	 * @return void
	 */
	public function addSpokenLanguages(ISpokenLanguage $language);
	/**
	 * @return void
	 */
	public function clearSpokenLanguages();

	/**
	 * @return IConfigurationManagementType[]
	 */
	public function getConfigurationManagementExpertises();

	public function addConfigurationManagementExpertise(IConfigurationManagementType $expertise);

	public function clearConfigurationManagementExpertises();

	/**
	 * @return IOpenStackComponent[]
	 */
	public function getExpertiseAreas();
	/**
	 * @param IOpenStackComponent $component
	 * @return void
	 */
	public function addExpertiseArea(IOpenStackComponent $component);
	/**
	 * @return void
	 */
	public function clearExpertiseAreas();

	/**
	 * @return IConsultantServiceOfferedType[]
	 */
	public function getServicesOffered();

	/**
	 * @param IConsultantServiceOfferedType $service
	 * @param IRegion $region
	 * @return void
	 */
	public function addServiceOffered(IConsultantServiceOfferedType $service, IRegion $region);

	/**
	 * @return void
	 */
	public function clearServicesOffered();

} 