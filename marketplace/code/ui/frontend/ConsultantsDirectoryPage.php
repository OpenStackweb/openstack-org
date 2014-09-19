<?php
/**
 * Class ConsultantsDirectoryPage
 */
class ConsultantsDirectoryPage extends MarketPlaceDirectoryPage
{
	static $allowed_children = "none";
}
/**
 * Class ConsultantsDirectoryPage_Controller
 */
class ConsultantsDirectoryPage_Controller extends MarketPlaceDirectoryPage_Controller {

	private static $allowed_actions = array('handleIndex');
	/**
	 * @var IEntityRepository
	 */
	private $consultant_repository;
	/**
	 * @var IEntityRepository
	 */
	private $region_repository;

	/**
	 * @var IConsultantsOfficesLocationsQueryHandler
	 */
	private $consultants_locations_query;

	/**
	 * @var IQueryHandler
	 */
	private $consultants_service_query;

	static $url_handlers = array(
		'$Company!/$Slug!' => 'handleIndex',
	);

	/**
	 * @var ConsultantManager
	 */
	private $manager;

	function init()	{
		parent::init();
		Requirements::customScript("jQuery(document).ready(function($) {
            $('#consulting','.marketplace-nav').addClass('current');
        });");
		Requirements::css("themes/openstack/javascript/jquery-ui-1.10.3.custom/css/smoothness/jquery-ui-1.10.3.custom.min.css");
		Requirements::css("themes/openstack/css/chosen.css", "screen,projection");
		Requirements::javascript("themes/openstack/javascript/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js");
		Requirements::javascript(Director::protocol()."maps.googleapis.com/maps/api/js?sensor=false");
		Requirements::javascript("marketplace/code/ui/admin/js/utils.js");
		Requirements::javascript("marketplace/code/ui/frontend/js/markerclusterer.js");
		Requirements::javascript("marketplace/code/ui/frontend/js/oms.min.js");
		Requirements::javascript("marketplace/code/ui/frontend/js/infobubble-compiled.js");
		Requirements::javascript("marketplace/code/ui/frontend/js/google.maps.jquery.js");
		Requirements::javascript("themes/openstack/javascript/chosen.jquery.min.js");
		Requirements::javascript("marketplace/code/ui/frontend/js/consultants.directory.page.js");
		Requirements::customScript($this->GATrackingCode());

		$this->consultant_repository       = new SapphireConsultantRepository;
		$this->region_repository           = new SapphireRegionRepository;
		$this->consultants_locations_query = new ConsultantsOfficesLocationsQueryHandler;
		$this->consultants_service_query   = new ConsultantsServicesQueryHandler;

		$google_geo_coding_api_key     = null;
		$google_geo_coding_client_id   = null;
		$google_geo_coding_private_key = null;
		if(defined('GOOGLE_GEO_CODING_API_KEY')){
			$google_geo_coding_api_key = GOOGLE_GEO_CODING_API_KEY;
		}
		else if (defined('GOOGLE_GEO_CODING_CLIENT_ID') && defined('GOOGLE_GEO_CODING_PRIVATE_KEY')){
			$google_geo_coding_client_id   = GOOGLE_GEO_CODING_CLIENT_ID;
			$google_geo_coding_private_key = GOOGLE_GEO_CODING_PRIVATE_KEY;
		}

		$this->manager = new ConsultantManager (
			$this->consultant_repository,
			new SapphireMarketPlaceVideoTypeRepository,
			new SapphireMarketPlaceTypeRepository,
			new SapphireOpenStackApiVersionRepository,
			new SapphireOpenStackComponentRepository,
			new SapphireOpenStackReleaseRepository,
			new SapphireRegionRepository,
			new SapphireSupportChannelTypeRepository,
			new SapphireSpokenLanguageRepository,
			new SapphireConfigurationManagementTypeRepository,
			new SapphireConsultantServiceOfferedTypeRepository,
			new ConsultantAddPolicy,
			new CompanyServiceCanAddResourcePolicy,
			new CompanyServiceCanAddVideoPolicy,
			new ConsultantFactory,
			new MarketplaceFactory,
			new ValidatorFactory,
			new OpenStackApiFactory,
			new GoogleGeoCodingService(
				new SapphireGeoCodingQueryRepository,
				new UtilFactory,
				SapphireTransactionManager::getInstance(),
				$google_geo_coding_api_key,
				$google_geo_coding_client_id,
				$google_geo_coding_private_key),
			null,
			new SessionCacheService,
			SapphireTransactionManager::getInstance()
		);

	}

	public function handleIndex() {
		$params = $this->request->allParams();
		if(isset($params["Company"]) && isset($params["Slug"])){
			//render instance ...
			return $this->consultant();
		}
	}

	public function getConsultants(){
		//return on view model
		return new ArrayList($this->manager->getActives());
	}

	public function consultant(){
		try{
			$params              = $this->request->allParams();
			$company_url_segment = Convert::raw2sql($params["Company"]);
			$slug                = Convert::raw2sql($params["Slug"]);
			$query               = new QueryObject();
			$query->addAddCondition(QueryCriteria::equal('Slug',$slug));
			$consultant       = $this->consultant_repository->getBy($query);
			if(!$consultant) throw new NotFoundEntityException('Consultant','by slug');
			if($consultant->getCompany()->URLSegment != $company_url_segment) throw new NotFoundEntityException('','');
			Requirements::javascript("marketplace/code/ui/frontend/js/consultant.page.js");
			$services = $consultant->getServicesOffered();
			$unique_services = array();
			$unique_regions  = array();
			foreach($services as $service){
				if(!array_key_exists($service->getType(),$unique_services))
					$unique_services[$service->getType()] = $service;
				if(!array_key_exists($service->getRegionID(),$unique_regions)){
					$region = $this->region_repository->getById($service->getRegionID());
					$unique_regions[$service->getRegionID()]=$region;
				}
			}
			return $this->Customise(
				array(
					'Consultant' => $consultant,
					'Services'   => new ArrayList(array_values($unique_services)),
					'Regions'    => new ArrayList(array_values($unique_regions)),
				)
			)->renderWith(array('ConsultantsDirectoryPage_consultant','ConsultantsDirectoryPage','MarketPlacePage'));
		}
		catch (Exception $ex) {
			return $this->httpError(404, 'Sorry that Consultant could not be found!.');
		}
	}

	public function getAllOfficesLocationsJson(){
		$query = new QueryObject;
		$query->addAddCondition(QueryCriteria::equal("Active",true));
		list($list,$size) = $this->consultant_repository->getAll($query,0,100);
		$res = array();
		foreach($list as $consultant){
			$color = strtoupper(dechex(rand(0,10000000)));
			$office_index = 1;
			foreach($consultant->getOffices() as $office){
				$data_office = array();
				$data_office['color']   = is_null($consultant->getCompany()->Color)?$color:$consultant->getCompany()->Color;
				$address = $office->getAddress();
				$data_office['address'] = '';
				if(!empty($address))
					$data_office['address'] = trim($address.' '.$office->getAddress1());
				$state = $office->getState();
				if(!empty($state)){
					$data_office['address'] .= ', '.$state;
				}
				$data_office['address'] .=((empty($data_office['address']))?'': ', ').$office->getCity();
				$data_office['address'] .= ', '.$office->getCountry();
				$data_office['lat']     = $office->getLat();
				$data_office['lng']     = $office->getLng();
				$data_office['owner']   = $consultant->getName();
				$data_office['name']    = sprintf('Office #%s',$office_index);
				++$office_index;
				array_push($res,$data_office);
			}
		}
		return json_encode($res);
	}

	public function getCurrentOfficesLocationsJson(){
		$res = array();
		$params              = $this->request->allParams();
		$company_url_segment = Convert::raw2sql($params["Company"]);
		$slug                = Convert::raw2sql($params["Slug"]);
		$query               = new QueryObject();
		$query->addAddCondition(QueryCriteria::equal('Slug',$slug));
		$consultant       = $this->consultant_repository->getBy($query);
		if(!$consultant) throw new NotFoundEntityException('Consultant','by slug');
		if($consultant->getCompany()->URLSegment != $company_url_segment) throw new NotFoundEntityException('','');
		$color = strtoupper(dechex(rand(0,10000000)));
		$office_index = 1;
		foreach($consultant->getOffices() as $office){
				$data_office = array();
				$data_office['color']   = is_null($consultant->getCompany()->Color)?$color:$consultant->getCompany()->Color;
				$address = $office->getAddress();
				$data_office['address'] = '';
				if(!empty($address))
					$data_office['address'] = trim($address.' '.$office->getAddress1());
				$state = $office->getState();
				if(!empty($state)){
					$data_office['address'] .= ', '.$state;
				}
				$data_office['address'] .=((empty($data_office['address']))?'': ', ').$office->getCity();
				$data_office['address'] .= ', '.$office->getCountry();
				$data_office['lat']     = $office->getLat();
				$data_office['lng']     = $office->getLng();
				$data_office['owner']   = $consultant->getName();
				$data_office['name']    = sprintf('Office #%s',$office_index);
				++$office_index;
				array_push($res,$data_office);
		}
		return json_encode($res);
	}

	public function ServicesCombo(){
		$source = array();
		$result = $this->consultants_service_query->handle(new OpenStackImplementationNamesQuerySpecification(''));
		foreach($result->getResult() as $dto){
			$source[$dto->getValue()] =  $dto->getValue();
		}
		$ddl = new DropdownField('service-term"',$title=null,$source);
		$ddl->setEmptyString('-- Show All --');
		return $ddl;
	}

	public function LocationCombo(){
		$source = array();
		$result = $this->consultants_locations_query->handle(new OpenStackImplementationNamesQuerySpecification(''));
		foreach($result->getResult() as $dto){
			$source[$dto->getValue()] =  $dto->getValue();
		}
		$ddl = new DropdownField('location-term"',$title = null,$source);
		$ddl->setEmptyString('-- Show All --');
		return $ddl;
	}
}