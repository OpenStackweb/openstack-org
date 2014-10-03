<?php
/**
 * Class DistributionCrudApi
 */
final class DistributionCrudApi extends CompanyServiceCrudApi {

	private $marketplace_type_repository;
	private $distribution_repository;


	public function __construct() {

		$this->distribution_repository     = new SapphireDistributionRepository;
		$this->marketplace_type_repository = new SapphireMarketPlaceTypeRepository;

		$manager = new DistributionManager (
			$this->distribution_repository,
			new SapphireMarketPlaceVideoTypeRepository,
			$this->marketplace_type_repository,
			new SapphireGuestOSTypeRepository,
			new SapphireHyperVisorTypeRepository,
			new SapphireOpenStackApiVersionRepository,
			new SapphireOpenStackComponentRepository,
			new SapphireOpenStackReleaseRepository,
			new SapphireRegionRepository,
			new SapphireSupportChannelTypeRepository,
			new SapphireOpenStackReleaseSupportedApiVersionRepository,
			new DistributionAddPolicy($this->distribution_repository, $this->marketplace_type_repository),
			new CompanyServiceCanAddResourcePolicy,
			new CompanyServiceCanAddVideoPolicy,
			new DistributionFactory,
			new MarketplaceFactory,
			new ValidatorFactory,
			new OpenStackApiFactory,
			null,
			new SessionCacheService,
			SapphireTransactionManager::getInstance()
		);


		parent::__construct($manager,new DistributionFactory);
		// filters ...
		$this_var     = $this;
		$current_user = $this->current_user;
		$repository   = $this->distribution_repository;

		$this->addBeforeFilter('addCompanyService','check_add_company',function ($request) use($this_var, $current_user, $repository){
			$data = $this_var->getJsonRequest();
			if (!$data) return $this->serverError();

			$company_id = intval(@$data['company_id']);

			if(!$current_user->isMarketPlaceAdminOfCompany(IDistribution::MarketPlaceGroupSlug, $company_id))
				return $this_var->permissionFailure();
		});

		$this->addBeforeFilter('updateCompanyService','check_update_company',function ($request) use($this_var, $current_user){
			$data = $this_var->getJsonRequest();
			if (!$data) return $this->serverError();
			if(!$current_user->isMarketPlaceAdminOfCompany(IDistribution::MarketPlaceGroupSlug,intval(@$data['company_id'])))
				return $this_var->permissionFailure();
		});

		$this->addBeforeFilter('deleteCompanyService','check_delete_company',function ($request) use($this_var, $current_user,$repository){
			$company_service_id = intval($request->param('COMPANY_SERVICE_ID'));
			$company_service    = $repository->getById($company_service_id);
			if(!$current_user->isMarketPlaceAdminOfCompany(IDistribution::MarketPlaceGroupSlug, $company_service->getCompany()->getIdentifier()))
				return $this_var->permissionFailure();
		});
	}

	/**
	 * @var array
	 */
	static $url_handlers = array(
		'GET $COMPANY_SERVICE_ID!'    => 'getDistribution',
		'DELETE $COMPANY_SERVICE_ID!' => 'deleteCompanyService',
		'POST '                       => 'addCompanyService',
		'PUT '                        => 'updateCompanyService',
	);

	/**
	 * @var array
	 */
	static $allowed_actions = array(
		'getDistribution',
		'deleteCompanyService',
		'addCompanyService',
		'updateCompanyService'
	);

	public function getDistribution(){
		$company_service_id  = intval($this->request->param('COMPANY_SERVICE_ID'));
		$distribution = $this->distribution_repository->getById($company_service_id);
		if(!$distribution)
			return $this->notFound();
		return $this->ok(OpenStackImplementationAssembler::convertOpenStackImplementationToArray($distribution));
	}

	public function addCompanyService(){
		try {
			return parent::addCompanyService();
		}
		catch (Exception $ex) {
			SS_Log::log($ex,SS_Log::ERR);
			return $this->serverError();
		}
	}

	public function updateCompanyService(){
		try {
			return parent::updateCompanyService();
		}
		catch (Exception $ex) {
			SS_Log::log($ex,SS_Log::ERR);
			return $this->serverError();
		}
	}
}