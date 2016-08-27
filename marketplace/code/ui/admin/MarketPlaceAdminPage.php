<?php

/**
 * Copyright 2014 Openstack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/

/**
 * Class MarketPlaceAdminPage
 * Defines MarketPlace Admin area
 */
class MarketPlaceAdminPage extends Page implements PermissionProvider
{
    static $db = array();

    static $has_one = array();

    function providePermissions()
    {
        return array(
            "MARKETPLACE_ADMIN_ACCESS" => "Access the MarketPlace Admin"
        );
    }
}

/**
 * Class MarketPlaceAdminPage_Controller
 */
class MarketPlaceAdminPage_Controller extends AdminController
{
    /**
     * @var IMarketplaceTypeRepository
     */
    private $marketplace_repository;
    /**
     * @var ICompaniesWithMarketPlaceCreationRightsQueryHandler
     */
    private $companies_with_marketplace_creation_rights;
    /**
     * @var ICompanyServiceRepository
     */
    private $distribution_repository;
    /**
     * @var ICompanyServiceRepository
     */
    private $distribution_draft_repository;
    /**
     * @var ICompanyServiceRepository
     */
    private $appliance_repository;
    /**
     * @var ICompanyServiceRepository
     */
    private $appliance_draft_repository;
    /**
     * @var IOpenStackComponentRepository
     */
    private $components_repository;
    /**
     * @var IEntityRepository
     */
    private $hyper_visors_repository;
    /**
     * @var IEntityRepository
     */
    private $guests_os_repository;
    /**
     * @var IEntityRepository
     */
    private $video_type_repository;
    /**
     * @var IEntityRepository
     */
    private $support_channel_types_repository;
    /**
     * @var IEntityRepository
     */
    private $region_repository;
    /**
     * @var IEntityRepository
     */
    private $pricing_schema_repository;
    /**
     * @var IEntityRepository
     */
    private $public_clouds_repository;
    /**
     * @var IEntityRepository
     */
    private $public_clouds_draft_repository;
    /**
     * @var IEntityRepository
     */
    private $private_clouds_repository;
    /**
     * @var IEntityRepository
     */
    private $private_clouds_draft_repository;
    /**
     * @var IEntityRepository
     */
    private $config_management_type_repository;
    /**
     * @var IEntityRepository
     */
    private $consultant_service_offered_type_repository;
    /**
     * @var IEntityRepository
     */
    private $consultant_repository;
    /**
     * @var IEntityRepository
     */
    private $consultant_draft_repository;


    /**
     * @var IEntityRepository
     */
    private $interop_program_repository;

    function init()
    {
        parent::init();
        //check permissions
        if (!Member::currentUser() || !Member::currentUser()->isMarketPlaceAdmin())
            return Security::permissionFailure();
        //css
        Requirements::css(Director::protocol() . "code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css");
        Requirements::css(Director::protocol() . "fonts.googleapis.com/css?family=PT+Sans&subset=latin");

        $css_files =  array(
            "marketplace/code/ui/admin/css/layout.css",
            "marketplace/code/ui/admin/css/marketplace.admin.css",
            "themes/openstack/css/chosen.css",
            "themes/openstack/css/colorpicker.css",
        );

        foreach($css_files as $css_file)
            Requirements::css($css_file);

        if (Director::isDev())
            Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js");
        else
            Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");

        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/additional-methods.min.js");
        Requirements::javascript(Director::protocol() . "code.jquery.com/ui/1.10.4/jquery-ui.min.js");

        $js_files =  array(
            "themes/openstack/javascript/chosen.jquery.min.js",
            "themes/openstack/javascript/jquery.jsonp-2.4.0.min.js",
            "themes/openstack/javascript/jquery.validate.custom.methods.js",
            "themes/openstack/javascript/pure.min.js",
            "themes/openstack/javascript/jquery.serialize.js",
            "themes/openstack/javascript/jquery.cleanform.js",
            'marketplace/code/ui/admin/js/utils.js',
            "marketplace/code/ui/admin/js/marketplaceadmin.js",
            "marketplace/code/ui/admin/js/implementations.js",
            'marketplace/code/ui/admin/js/public_clouds.js',
            'marketplace/code/ui/admin/js/private_clouds.js',
            'marketplace/code/ui/admin/js/consultants.js',
            "themes/openstack/javascript/colorpicker.js"
        );

        foreach($js_files as $js_file)
            Requirements::javascript($js_file);

        // model
        $this->marketplace_repository = new SapphireMarketPlaceTypeRepository;
        $this->companies_with_marketplace_creation_rights = new CompaniesWithMarketplaceCreationRightsSapphireQueryHandler;
        $this->distribution_repository = new SapphireDistributionRepository;
        $this->distribution_draft_repository = new SapphireDistributionRepository(true);
        $this->appliance_repository = new SapphireApplianceRepository;
        $this->appliance_draft_repository = new SapphireApplianceRepository(true);
        $this->components_repository = new SapphireOpenStackComponentRepository;
        $this->hyper_visors_repository = new SapphireHyperVisorTypeRepository;
        $this->guests_os_repository = new SapphireGuestOSTypeRepository();
        $this->video_type_repository = new SapphireMarketPlaceVideoTypeRepository;
        $this->support_channel_types_repository = new SapphireSupportChannelTypeRepository;
        $this->region_repository = new SapphireRegionRepository;
        $this->pricing_schema_repository = new SapphirePricingSchemaRepository;
        $this->public_clouds_repository = new SapphirePublicCloudRepository;
        $this->public_clouds_draft_repository = new SapphirePublicCloudRepository(true);
        $this->private_clouds_repository = new SapphirePrivateCloudRepository;
        $this->private_clouds_draft_repository = new SapphirePrivateCloudRepository(true);
        $this->config_management_type_repository = new SapphireConfigurationManagementTypeRepository;
        $this->consultant_service_offered_type_repository = new SapphireConsultantServiceOfferedTypeRepository;
        $this->consultant_repository = new SapphireConsultantRepository;
        $this->consultant_draft_repository = new SapphireConsultantRepository(true);
        $this->interop_program_repository = new SapphireInteropProgramVersion();
    }


    public function index()
    {
        if ($this->canAdmin('implementations'))
            return $this->getViewer('index')->process($this);
        else if ($this->canAdmin('public_clouds')) {
            return Controller::curr()->redirect($this->Link("public_clouds"));
        } else if ($this->canAdmin('private_clouds')) {
            return Controller::curr()->redirect($this->Link("private_clouds"));
        } else if ($this->canAdmin('consultants')) {
            return Controller::curr()->redirect($this->Link("consultants"));

        }
        return $this->httpError(401, 'Unauthorized: you do not have the proper rights to access this page.');
    }

    static $allowed_actions = array(
        'add',
        'distribution',
        'appliance',
        'public_clouds',
        'public_cloud',
        'private_clouds',
        'private_cloud',
        'consultants',
        'consultant',
        'preview',
        'draft_preview',
        'pdf',
        'draft_pdf',
    );


    /**
     * @var array
     */
    static $url_handlers = array(
        'GET $MARKETPLACETYPE/$ID/preview' => 'preview',
        'GET $MARKETPLACETYPE/$ID/draft_preview' => 'draft_preview',
        'GET $MARKETPLACETYPE/$ID/pdf' => 'pdf',
        'GET $MARKETPLACETYPE/$ID/draft_pdf' => 'draft_pdf',
    );

    public function getCurrentTab()
    {
        $current_tab = Session::get('marketplaceadmin.current.tab');
        if (empty($current_tab)) {
            $current_tab = 1;
        }
        return $current_tab;
    }

    public function setCurrentTab($tab)
    {
        Session::set('marketplaceadmin.current.tab', $tab);
    }

    /**
     * @return ArrayList
     */
    public function getCompanies()
    {
        $current_marketplace_type = '';
        $type_id = intval($this->request->getVar('type_id'));
        if ($type_id > 0) {
            //choose by current marketplace type ( we are adding a new instance)
            $type_id = intval($this->request->getVar('type_id'));
            if ($type_id > 0) {
                $product_type = $this->marketplace_repository->getById($type_id);
                if ($product_type) {
                    $current_marketplace_type = $product_type->getName();
                }
            }
        } else {
            //choose by current tab (listing)
            switch ($this->getCurrentTab()) {
                case 1: {
                    $current_marketplace_type = IOpenStackImplementation::AbstractMarketPlaceType;
                }
                    break;
                case 2:
                    $current_marketplace_type = IPublicCloudService::MarketPlaceType;
                    break;
                case 3:
                    $current_marketplace_type = IConsultant::MarketPlaceType;
                    break;
                case 4:
                    $current_marketplace_type = IPrivateCloudService::MarketPlaceType;
                    break;
            }
        }
        $result = $this->companies_with_marketplace_creation_rights->handle(new CompaniesWithMarketPlaceCreationRightsSpecification($current_marketplace_type));
        $ds = new ArrayList();
        foreach ($result->getResult() as $dto) {
            $ds->push(new CompanyViewModel($dto));
        }
        return $ds;
    }

    public function getDistributionMarketPlaceTypes()
    {
        $ds = new ArrayList();
        $ds->push($this->marketplace_repository->getByType(IDistribution::MarketPlaceType));
        $ds->push($this->marketplace_repository->getByType(IAppliance::MarketPlaceType));
        return $ds;
    }

    public function getMarketPlaceTypes()
    {
        $ds = new ArrayList();
        if ($this->canAdmin('distributions'))
            $ds->push($this->marketplace_repository->getByType(IDistribution::MarketPlaceType));
        if ($this->canAdmin('appliances'))
            $ds->push($this->marketplace_repository->getByType(IAppliance::MarketPlaceType));
        if ($this->canAdmin('public_clouds'))
            $ds->push($this->marketplace_repository->getByType(IPublicCloudService::MarketPlaceType));
        if ($this->canAdmin('private_clouds'))
            $ds->push($this->marketplace_repository->getByType(IPrivateCloudService::MarketPlaceType));
        if ($this->canAdmin('consultants'))
            $ds->push($this->marketplace_repository->getByType(IConsultant::MarketPlaceType));
        return $ds;
    }

    public function getCurrentDistribution()
    {
        $distribution_id = intval($this->request->getVar('id'));
        $is_draft = intval($this->request->getVar('is_draft'));
        $distribution = false;
        if ($distribution_id > 0) {
            if ($is_draft) {
                $distribution = $this->distribution_draft_repository->getById($distribution_id);
            } else {
                $distribution = $this->distribution_draft_repository->getByLiveServiceId($distribution_id);
            }

            //if no draft found we pull the live one to create the draft from it when saved
            if (!$distribution) {
                $distribution = $this->distribution_repository->getById($distribution_id);
            }
        }
        return $distribution;
    }

    public function getCurrentAppliance()
    {
        $appliance_id = intval($this->request->getVar('id'));
        $is_draft = intval($this->request->getVar('is_draft'));

        $appliance = false;
        if ($appliance_id > 0) {
            if ($is_draft) {
                $appliance = $this->appliance_draft_repository->getById($appliance_id);
            } else {
                $appliance = $this->appliance_draft_repository->getByLiveServiceId($appliance_id);
            }

            //if no draft found we pull the live one to create the draft from it when saved
            if (!$appliance) {
                $appliance = $this->appliance_repository->getById($appliance_id);
            }
        }
        return $appliance;
    }

    public function getOpenStackAvailableComponents()
    {
        $query = new QueryObject;
        $query->addOrder(QueryOrder::asc('Name'));
        list($list, $size) = $this->components_repository->getAll($query, 0, 1000);
        return new ArrayList($list);
    }

    public function getCurrentDistributionJson()
    {
        $distribution = $this->getCurrentDistribution();
        if ($distribution) {
            return json_encode(OpenStackImplementationAssembler::convertOpenStackImplementationToArray($distribution));
        }
    }

    public function getCurrentApplianceJson()
    {
        $appliance = $this->getCurrentAppliance();
        if ($appliance) {
            return json_encode(OpenStackImplementationAssembler::convertOpenStackImplementationToArray($appliance));
        }
    }

    public function ReleasesByComponent()
    {
        $res = array();
        $query = new QueryObject;
        list($list, $size) = $this->components_repository->getAll($query, 0, 999999);
        foreach ($list as $component) {
            $res2 = array();
            $releases = $component->getSupportedReleases();
            foreach ($releases as $release) {
                array_push($res2, array("id" => $release->getIdentifier(), "name" => $release->getName()));
            }
            $res[$component->getCodeName()] = $res2;
        }
        return json_encode($res);
    }


    public function getHyperVisors()
    {
        list($list, $size) = $this->hyper_visors_repository->getAll(new QueryObject);
        return new ArrayList($list);
    }

    public function getGuestsOS()
    {
        list($list, $size) = $this->guests_os_repository->getAll(new QueryObject);
        return new ArrayList($list);
    }

    public function getVideoTypes()
    {
        list($list, $size) = $this->video_type_repository->getAll(new QueryObject);
        return new ArrayList($list);
    }

    public function getSupportChannelTypes()
    {
        list($list, $size) = $this->support_channel_types_repository->getAll(new QueryObject);
        return new ArrayList($list);
    }

    public function  getAvailableRegions()
    {
        list($list, $size) = $this->region_repository->getAll(new QueryObject);
        return new ArrayList($list);
    }

    public function add()
    {
        $type_id = intval($this->request->getVar('type_id'));
        if ($type_id > 0) {
            $product_type = $this->marketplace_repository->getById($type_id);
            if ($product_type) {
                switch ($product_type->getName()) {
                    case IAppliance::MarketPlaceType: {
                        return $this->appliance();
                    }
                        break;
                    case IDistribution::MarketPlaceType: {
                        return $this->distribution();
                    }
                        break;
                    case IPublicCloudService::MarketPlaceType: {
                        return $this->public_cloud();
                    }
                        break;
                    case IPrivateCloudService::MarketPlaceType: {
                        return $this->private_cloud();
                    }
                        break;
                    case IConsultant::MarketPlaceType: {
                        return $this->consultant();
                    }
                        break;
                }
            }
            Controller::curr()->redirect($this->Link());
        }
        Controller::curr()->redirect($this->Link());
    }

    public function distribution()
    {
        Requirements::javascript('marketplace/code/ui/admin/js/utils.js');
        Requirements::javascript('marketplace/code/ui/admin/js/jquery.text.area.counter.js');
        Requirements::javascript('marketplace/code/ui/admin/js/hypervisors.js');
        Requirements::javascript('marketplace/code/ui/admin/js/guest.os.js');
        Requirements::javascript('marketplace/code/ui/admin/js/components.js');
        Requirements::javascript('marketplace/code/ui/admin/js/videos.js');
        Requirements::javascript('marketplace/code/ui/admin/js/support.channels.js');
        Requirements::javascript('marketplace/code/ui/admin/js/additional.resources.js');
        Requirements::javascript('marketplace/code/ui/admin/js/marketplace.type.header.js');
        Requirements::javascript('marketplace/code/ui/admin/js/implementation_openstack_powered.js');
        HtmlEditorField::include_js();
        Requirements::javascript('marketplace/code/ui/admin/js/distribution.js');

        return $this->getViewer('distribution')->process($this);
    }

    public function appliance()
    {
        Requirements::javascript('marketplace/code/ui/admin/js/utils.js');
        Requirements::javascript('marketplace/code/ui/admin/js/jquery.text.area.counter.js');
        Requirements::javascript('marketplace/code/ui/admin/js/hypervisors.js');
        Requirements::javascript('marketplace/code/ui/admin/js/guest.os.js');
        Requirements::javascript('marketplace/code/ui/admin/js/components.js');
        Requirements::javascript('marketplace/code/ui/admin/js/videos.js');
        Requirements::javascript('marketplace/code/ui/admin/js/support.channels.js');
        Requirements::javascript('marketplace/code/ui/admin/js/additional.resources.js');
        Requirements::javascript('marketplace/code/ui/admin/js/marketplace.type.header.js');
        Requirements::javascript('marketplace/code/ui/admin/js/implementation_openstack_powered.js');
        HtmlEditorField::include_js();
        Requirements::javascript('marketplace/code/ui/admin/js/appliance.js');

        return $this->getViewer('appliance')->process($this);
    }

    public function public_cloud()
    {
        Requirements::javascript('marketplace/code/ui/admin/js/utils.js');
        Requirements::javascript('marketplace/code/ui/admin/js/jquery.text.area.counter.js');
        Requirements::javascript('marketplace/code/ui/admin/js/hypervisors.js');
        Requirements::javascript('marketplace/code/ui/admin/js/guest.os.js');
        Requirements::javascript('marketplace/code/ui/admin/js/components.js');
        Requirements::javascript('marketplace/code/ui/admin/js/videos.js');
        Requirements::javascript('marketplace/code/ui/admin/js/support.channels.js');
        Requirements::javascript('marketplace/code/ui/admin/js/additional.resources.js');
        Requirements::javascript('marketplace/code/ui/admin/js/pricing.schemas.js');
        Requirements::javascript('marketplace/code/ui/admin/js/datacenter.locations.js');
        Requirements::javascript('marketplace/code/ui/admin/js/marketplace.type.header.js');
        Requirements::javascript('marketplace/code/ui/admin/js/implementation_openstack_powered.js');
        Requirements::javascript(Director::protocol() . "maps.googleapis.com/maps/api/js?sensor=false");
        Requirements::javascript('marketplace/code/ui/admin/js/geocoding.jquery.js');
        HtmlEditorField::include_js();
        Requirements::javascript('marketplace/code/ui/admin/js/public_cloud.js');

        return $this->getViewer('public_cloud')->process($this);
    }

    public function private_cloud()
    {
        Requirements::javascript('marketplace/code/ui/admin/js/utils.js');
        Requirements::javascript('marketplace/code/ui/admin/js/jquery.text.area.counter.js');
        Requirements::javascript('marketplace/code/ui/admin/js/hypervisors.js');
        Requirements::javascript('marketplace/code/ui/admin/js/guest.os.js');
        Requirements::javascript('marketplace/code/ui/admin/js/components.js');
        Requirements::javascript('marketplace/code/ui/admin/js/videos.js');
        Requirements::javascript('marketplace/code/ui/admin/js/support.channels.js');
        Requirements::javascript('marketplace/code/ui/admin/js/additional.resources.js');
        Requirements::javascript('marketplace/code/ui/admin/js/pricing.schemas.js');
        Requirements::javascript('marketplace/code/ui/admin/js/datacenter.locations.js');
        Requirements::javascript('marketplace/code/ui/admin/js/marketplace.type.header.js');
        Requirements::javascript('marketplace/code/ui/admin/js/implementation_openstack_powered.js');
        Requirements::javascript(Director::protocol() . "maps.googleapis.com/maps/api/js?sensor=false");
        Requirements::javascript('marketplace/code/ui/admin/js/geocoding.jquery.js');
        HtmlEditorField::include_js();
        Requirements::javascript('marketplace/code/ui/admin/js/private_cloud.js');
        return $this->getViewer('private_cloud')->process($this);
    }

    public function consultant()
    {
        Requirements::javascript('marketplace/code/ui/admin/js/utils.js');
        Requirements::javascript('marketplace/code/ui/admin/js/jquery.text.area.counter.js');
        Requirements::javascript('marketplace/code/ui/admin/js/videos.js');
        Requirements::javascript('marketplace/code/ui/admin/js/support.channels.js');
        Requirements::javascript('marketplace/code/ui/admin/js/additional.resources.js');
        Requirements::javascript('marketplace/code/ui/admin/js/reference.clients.js');
        Requirements::javascript('marketplace/code/ui/admin/js/services.offered.js');
        Requirements::javascript('marketplace/code/ui/admin/js/expertise.areas.js');
        Requirements::javascript('marketplace/code/ui/admin/js/configuration.management.expertise.js');
        Requirements::javascript('marketplace/code/ui/admin/js/spoken.languages.js');
        Requirements::javascript('marketplace/code/ui/admin/js/offices.js');
        Requirements::javascript('marketplace/code/ui/admin/js/marketplace.type.header.js');
        Requirements::javascript(Director::protocol() . "maps.googleapis.com/maps/api/js?sensor=false");
        Requirements::javascript('marketplace/code/ui/admin/js/geocoding.jquery.js');
        Requirements::javascript('marketplace/code/ui/admin/js/consultant.js');
        return $this->getViewer('consultant')->process($this);
    }

    public function getConfigurationManagementTypes()
    {
        $query = new QueryObject(new ConfigurationManagementType);
        $query->addOrder(QueryOrder::asc('Type'));
        list($list, $size) = $this->config_management_type_repository->getAll($query, 0, 1000);
        return new ArrayList($list);
    }

    public function getServicesOffered()
    {
        $query = new QueryObject(new ConsultantServiceOfferedType);
        $query->addOrder(QueryOrder::asc('Type'));
        list($list, $size) = $this->consultant_service_offered_type_repository->getAll($query, 0, 1000);
        return new ArrayList($list);
    }

    public function sortCompanyService(&$query, $sort, $dir)
    {
        switch ($sort) {
            case 'company': {
                $query->addAlias(QueryAlias::create('Company'));
                if ($dir == 'asc')
                    $query->addOrder(QueryOrder::asc('Company.Name'));
                else
                    $query->addOrder(QueryOrder::desc('Company.Name'));
            }
                break;
            case 'name': {
                if ($dir == 'asc')
                    $query->addOrder(QueryOrder::asc('Name'));
                else
                    $query->addOrder(QueryOrder::desc('Name'));
            }
            case 'type': {
                $query->addAlias(QueryAlias::create('MarketPlaceType'));
                if ($dir == 'asc') {
                    $query->addOrder(QueryOrder::asc('MarketPlaceType.Name'));
                } else {
                    $query->addOrder(QueryOrder::desc('MarketPlaceType.Name'));
                }
            }
            case 'status': {
                if ($dir == 'asc')
                    $query->addOrder(QueryOrder::asc('Active'));
                else
                    $query->addOrder(QueryOrder::desc('Active'));
            }
                break;
            case 'updated': {
                if ($dir == 'asc')
                    $query->addOrder(QueryOrder::asc('LastEdited'));
                else
                    $query->addOrder(QueryOrder::desc('LastEdited'));
            }
                break;
            case 'updatedby': {
                $query->addAlias(QueryAlias::create('EditedBy'));
                if ($dir == 'asc')
                    $query->addOrder(QueryOrder::asc('Member.Email'));
                else
                    $query->addOrder(QueryOrder::desc('Member.Email'));
            }
                break;
            default: {
                if ($dir == 'asc')
                    $query->addOrder(QueryOrder::asc('ID'));
                else
                    $query->addOrder(QueryOrder::desc('ID'));
            }
                break;
        }
    }

    public function getConsultants()
    {
        $product_name = trim(Convert::raw2sql($this->request->getVar('name')));
        $company_id = intval($this->request->getVar('company_id'));
        $sort = $this->request->getVar('sort');
        $query = new QueryObject(new CompanyService);
        $query_draft = new QueryObject(new CompanyServiceDraft);
        $query_draft->addAndCondition(QueryCriteria::equal('LiveServiceID', 0)); //only drafts without live version

        $query->addAlias(QueryAlias::create('Company'));
        $query_draft->addAlias(QueryAlias::create('Company'));

        if (!empty($product_name)) {
            $query->addOrCondition(QueryCriteria::like('CompanyService.Name', $product_name));
            $query->addOrCondition(QueryCriteria::like('Company.Name', $product_name));
            $query_draft->addOrCondition(QueryCriteria::like('CompanyServiceDraft.Name', $product_name));
            $query_draft->addOrCondition(QueryCriteria::like('Company.Name', $product_name));
        }
        if ($company_id > 0) {
            $query->addAndCondition(QueryCriteria::equal('Company.ID', $company_id));
            $query_draft->addAndCondition(QueryCriteria::equal('Company.ID', $company_id));
        }

        //set sorting
        if (!empty($sort)) {
            $dir = $this->getSortDir('consultants');
            $this->sortCompanyService($query, $sort, $dir);
            $this->sortCompanyService($query_draft, $sort, $dir);
        }
        //get consultants
        list($list1, $size1) = $this->consultant_repository->getAll($query, 0, 1000);
        list($list2, $size2) = $this->consultant_draft_repository->getAll($query_draft, 0, 1000);

        //return on view model
        return new ArrayList(array_merge($list1, $list2));

    }

    public function getCurrentConsultant()
    {
        $consultant_id = intval($this->request->getVar('id'));
        $is_draft = intval($this->request->getVar('is_draft'));
        $consultant = false;
        if ($consultant_id > 0) {
            if ($is_draft) {
                $consultant = $this->consultant_draft_repository->getById($consultant_id);
            } else {
                $consultant = $this->consultant_draft_repository->getByLiveServiceId($consultant_id);
            }

            //if no draft found we pull the live one to create the draft from it when saved
            if (!$consultant) {
                $consultant = $this->consultant_repository->getById($consultant_id);
            }
        }
        return $consultant;
    }

    public function getCurrentConsultantJson()
    {
        $consultant = $this->getCurrentConsultant();
        if ($consultant) {
            return json_encode(ConsultantAssembler::convertConsultantToArray($consultant));
        }
    }

    public function getSortDir($type)
    {
        $default = 'asc';
        $dir = Session::get($type . '.sort.dir');
        if (empty($dir)) {
            $dir = $default;
        } else {
            $dir = $dir == 'asc' ? 'desc' : 'asc';
        }
        Session::set($type . '.sort.dir', $dir);
        return $dir;
    }

    public function getDistributions()
    {

        if (!$this->canAdmin('implementations')) {
            return $this->httpError(401, 'Unauthorized: you do not have the proper rights to access this page.');
        }

        $product_name = trim(Convert::raw2sql($this->request->getVar('name')));
        $implementation_type_id = intval($this->request->getVar('implementation_type_id'));
        $company_id = intval($this->request->getVar('company_id'));

        $sort = $this->request->getVar('sort');
        $query = new QueryObject(new CompanyService);
        $query_draft = new QueryObject(new CompanyServiceDraft);
        $query_draft->addAndCondition(QueryCriteria::equal('LiveServiceID', 0));

        if (!empty($product_name)) {
            $query->addAndCondition(QueryCriteria::like('Name', $product_name));
            $query_draft->addAndCondition(QueryCriteria::like('Name', $product_name));
        }
        if ($implementation_type_id > 0) {
            $query->addAndCondition(QueryCriteria::equal('MarketPlaceType.ID', $implementation_type_id));
            $query_draft->addAndCondition(QueryCriteria::equal('MarketPlaceType.ID', $implementation_type_id));
        }
        if ($company_id > 0) {
            $query->addAndCondition(QueryCriteria::equal('Company.ID', $company_id));
            $query_draft->addAndCondition(QueryCriteria::equal('Company.ID', $company_id));
        }
        //set sorting
        if (!empty($sort)) {
            $dir = $this->getSortDir('distributions');
            $this->sortCompanyService($query, $sort, $dir);
            $this->sortCompanyService($query_draft, $sort, $dir);
        }
        //get distributions
        $list1 = array();
        $list2 = array();
        $list3 = array();
        $list4 = array();

        if ($this->canAdmin('distributions')) {
            list($list1, $size1) = $this->distribution_repository->getAll($query, 0, 1000);
            list($list2, $size2) = $this->distribution_draft_repository->getAll($query_draft, 0, 1000);
        }

        if ($this->canAdmin('appliances')) {
            list($list3, $size3) = $this->appliance_repository->getAll($query, 0, 1000);
            list($list4, $size4) = $this->appliance_draft_repository->getAll($query_draft, 0, 1000);
        }

        //return on view model
        return new ArrayList(array_merge($list1, $list2, $list3, $list4));

    }

    /**
     * @return ArrayList
     */
    public function getPublicClouds()
    {

        $product_name = trim(Convert::raw2sql($this->request->getVar('name')));
        $company_id = intval($this->request->getVar('company_id'));
        $sort = $this->request->getVar('sort');
        $query = new QueryObject(new CompanyService);
        $query_draft = new QueryObject(new CompanyServiceDraft);
        $query_draft->addAndCondition(QueryCriteria::equal('LiveServiceID', 0));

        if (!empty($product_name)) {
            $query->addAndCondition(QueryCriteria::like('Name', $product_name));
            $query_draft->addAndCondition(QueryCriteria::like('Name', $product_name));
        }
        if ($company_id > 0) {
            $query->addAndCondition(QueryCriteria::equal('Company.ID', $company_id));
            $query_draft->addAndCondition(QueryCriteria::equal('Company.ID', $company_id));
        }

        //set sorting
        if (!empty($sort)) {
            $dir = $this->getSortDir('public.clouds');

            $this->sortCompanyService($query, $sort, $dir);
            $this->sortCompanyService($query_draft, $sort, $dir);
        }
        //get public clouds
        list($list1, $size1) = $this->public_clouds_repository->getAll($query, 0, 1000);
        list($list2, $size2) = $this->public_clouds_draft_repository->getAll($query_draft, 0, 1000);

        //return on view model

        return new ArrayList(array_merge($list1, $list2));

    }

    public function getCurrentPublicCloud()
    {
        $public_cloud_id = intval($this->request->getVar('id'));
        $is_draft = intval($this->request->getVar('is_draft'));
        $public_cloud = false;
        if ($public_cloud_id > 0) {
            if ($is_draft) {
                $public_cloud = $this->public_clouds_draft_repository->getById($public_cloud_id);
            } else {
                $public_cloud = $this->public_clouds_draft_repository->getByLiveServiceId($public_cloud_id);
            }

            //if no draft found we pull the live one to create the draft from it when saved
            if (!$public_cloud) {
                $public_cloud = $this->public_clouds_repository->getById($public_cloud_id);
            }
        }
        return $public_cloud;
    }

    public function getCurrentPublicCloudJson()
    {
        $public_cloud = $this->getCurrentPublicCloud();
        if ($public_cloud) {
            return json_encode(CloudAssembler::convertCloudToArray($public_cloud));
        }
    }

    /**
     * @return ArrayList
     */
    public function getPrivateClouds()
    {

        $product_name = trim(Convert::raw2sql($this->request->getVar('name')));
        $company_id = intval($this->request->getVar('company_id'));
        $sort = $this->request->getVar('sort');
        $query = new QueryObject(new CompanyService);
        $query_draft = new QueryObject(new CompanyServiceDraft);
        $query_draft->addAndCondition(QueryCriteria::equal('LiveServiceID', 0));

        if (!empty($product_name)) {
            $query->addAndCondition(QueryCriteria::like('Name', $product_name));
            $query_draft->addAndCondition(QueryCriteria::like('Name', $product_name));
        }
        if ($company_id > 0) {
            $query->addAndCondition(QueryCriteria::equal('Company.ID', $company_id));
            $query_draft->addAndCondition(QueryCriteria::equal('Company.ID', $company_id));
        }

        //set sorting
        if (!empty($sort)) {
            $dir = $this->getSortDir('private.clouds');

            $this->sortCompanyService($query, $sort, $dir);
            $this->sortCompanyService($query_draft, $sort, $dir);
        }
        //get public clouds
        list($list1, $size1) = $this->private_clouds_repository->getAll($query, 0, 1000);
        list($list2, $size2) = $this->private_clouds_draft_repository->getAll($query_draft, 0, 1000);

        //return on view model

        return new ArrayList(array_merge($list1, $list2));

    }

    public function getCurrentPrivateCloud()
    {
        $private_cloud_id = intval($this->request->getVar('id'));
        $is_draft = intval($this->request->getVar('is_draft'));
        $private_cloud = false;
        if ($private_cloud_id > 0) {
            if ($is_draft) {
                $private_cloud = $this->private_clouds_draft_repository->getById($private_cloud_id);
            } else {
                $private_cloud = $this->private_clouds_draft_repository->getByLiveServiceId($private_cloud_id);
            }

            //if no draft found we pull the live one to create the draft from it when saved
            if (!$private_cloud) {
                $private_cloud = $this->private_clouds_repository->getById($private_cloud_id);
            }
        }
        return $private_cloud;
    }

    public function getCurrentPrivateCloudJson()
    {
        $private_cloud = $this->getCurrentPrivateCloud();
        if ($private_cloud) {
            return json_encode(CloudAssembler::convertCloudToArray($private_cloud));
        }
    }

    public function getUsePricingSchema()
    {
        $type_id = intval($this->request->getVar('type_id'));
        if ($type_id > 0) {
            $product_type = $this->marketplace_repository->getById($type_id);
            if ($product_type->getName() == IPublicCloudService::MarketPlaceType) {
                return true;
            }
        }
        return $this->getCurrentPublicCloud();
    }

    public function getCountriesDDL($id = "add-datacenter-location-country")
    {
        $ddl = new CountryDropdownField($id, 'Country');
        $ddl->setEmptyString('-- select a country --');
        $ddl->addExtraClass('add-control');
        $ddl->addExtraClass('countries-ddl');
        return $ddl;
    }

    public function canAdmin($entity)
    {
        switch (strtolower(trim($entity))) {
            case 'implementations':
                return
                    Member::currentUser()->isMarketPlaceTypeAdmin(IDistribution::MarketPlaceGroupSlug) ||
                    Member::currentUser()->isMarketPlaceTypeAdmin(IAppliance::MarketPlaceGroupSlug);
                break;
            case 'appliances':
                return Member::currentUser()->isMarketPlaceTypeAdmin(IAppliance::MarketPlaceGroupSlug);
                break;
            case 'distributions':
                return Member::currentUser()->isMarketPlaceTypeAdmin(IDistribution::MarketPlaceGroupSlug);
                break;
            case 'consultants':
                return Member::currentUser()->isMarketPlaceTypeAdmin(IConsultant::MarketPlaceGroupSlug);
                break;
            case 'public_clouds':
                return Member::currentUser()->isMarketPlaceTypeAdmin(IPublicCloudService::MarketPlaceGroupSlug);
                break;
            case 'private_clouds':
                return Member::currentUser()->isMarketPlaceTypeAdmin(IPrivateCloudService::MarketPlaceGroupSlug);
                break;
        }
        return false;
    }

    public function isSuperAdmin()
    {
        return Member::currentUser()->isMarketPlaceSuperAdmin();
    }

    public function preview()
    {
        $marketplace_type = $this->request->param('MARKETPLACETYPE');
        $instance_id = intval($this->request->param('ID'));

        $query = new QueryObject();
        $query->addAndCondition(QueryCriteria::id('ID', $instance_id));
        Requirements::block("marketplace/code/ui/admin/css/marketplace.admin.css");

        Requirements::block(Director::protocol() . "code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css");

        switch (strtolower($marketplace_type)) {
            case 'distribution': {
                $distribution = $this->distribution_repository->getBy($query);
                if (!$distribution) throw new NotFoundEntityException('', '');
                $render = new DistributionSapphireRender($distribution);
                $distribution->IsPreview = true;
                return $render->draw();
            }
                break;
            case 'appliance': {
                $appliance = $this->appliance_repository->getBy($query);
                $appliance->IsPreview = true;
                $render = new ApplianceSapphireRender($appliance);
                return $render->draw();
            }
                break;
            case 'public_cloud': {
                $public_cloud = $this->public_clouds_repository->getBy($query);
                $public_cloud->IsPreview = true;
                if (!$public_cloud) throw new NotFoundEntityException('', '');
                $render = new PublicCloudSapphireRender($public_cloud);
                return $render->draw();
            }
                break;
            case 'private_cloud': {
                $private_cloud = $this->private_clouds_repository->getBy($query);
                $private_cloud->IsPreview = true;
                $render = new PrivateCloudSapphireRender($private_cloud);
                return $render->draw();

            }
                break;
            case 'consultant': {
                $consultant = $this->consultant_repository->getBy($query);
                if (!$consultant) throw new NotFoundEntityException('', '');
                $consultant->IsPreview = true;
                $render = new ConsultantSapphireRender($consultant);
                return $render->draw();
            }
                break;
            default:
                $this->httpError(404);
                break;
        }
    }

    public function draft_preview()
    {

        $marketplace_type = $this->request->param('MARKETPLACETYPE');
        $instance_id = intval($this->request->param('ID'));

        $query = new QueryObject();
        $query->addAndCondition(QueryCriteria::id('ID', $instance_id));
        Requirements::block("marketplace/code/ui/admin/css/marketplace.admin.css");

        Requirements::block(Director::protocol() . "code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css");

        switch (strtolower($marketplace_type)) {
            case 'distribution': {
                $distribution = $this->distribution_draft_repository->getBy($query);
                if (!$distribution) throw new NotFoundEntityException('', '');
                $render = new DistributionSapphireRender($distribution);
                $distribution->IsPreview = true;
                return $render->draw();
            }
                break;
            case 'appliance': {
                $appliance = $this->appliance_draft_repository->getBy($query);
                $appliance->IsPreview = true;
                $render = new ApplianceSapphireRender($appliance);
                return $render->draw();
            }
                break;
            case 'public_cloud': {
                $public_cloud = $this->public_clouds_draft_repository->getBy($query);
                $public_cloud->IsPreview = true;
                $public_cloud->IsDraft = true;
                if (!$public_cloud) throw new NotFoundEntityException('', '');
                $render = new PublicCloudSapphireRender($public_cloud);
                return $render->draw();
            }
                break;
            case 'private_cloud': {
                $private_cloud = $this->private_clouds_draft_repository->getBy($query);
                $private_cloud->IsPreview = true;
                $private_cloud->IsDraft = true;
                $render = new PrivateCloudSapphireRender($private_cloud);
                return $render->draw();

            }
                break;
            case 'consultant': {
                $consultant = $this->consultant_draft_repository->getBy($query);
                if (!$consultant) throw new NotFoundEntityException('', '');
                $consultant->IsPreview = true;
                $consultant->IsDraft = true;
                $render = new ConsultantSapphireRender($consultant);
                return $render->draw();
            }
                break;
            default:
                $this->httpError(404);
                break;
        }
    }

    public function getCurrentDataCenterLocationsJson()
    {
        $instance_id = intval($this->request->param('ID'));
        $marketplace_type = $this->request->param('MARKETPLACETYPE');
        $query = new QueryObject();
        $query->addAndCondition(QueryCriteria::id('ID', $instance_id));
        switch (strtolower($marketplace_type)) {
            case 'public_cloud': {
                $cloud = $this->public_clouds_repository->getBy($query);
            }
                break;
            case 'private_cloud': {
                $cloud = $this->private_clouds_repository->getBy($query);
            }
                break;

        }

        if (!$cloud) throw new NotFoundEntityException('', '');
        return CloudViewModel::getDataCenterLocationsJson($cloud);
    }

    public function getCurrentDataCenterLocationsDraftJson()
    {
        $instance_id = intval($this->request->param('ID'));
        $marketplace_type = $this->request->param('MARKETPLACETYPE');
        $query = new QueryObject();
        $query->addAndCondition(QueryCriteria::id('ID', $instance_id));
        switch (strtolower($marketplace_type)) {
            case 'public_cloud': {
                $cloud = $this->public_clouds_draft_repository->getBy($query);
            }
                break;
            case 'private_cloud': {
                $cloud = $this->private_clouds_draft_repository->getBy($query);
            }
                break;

        }

        if (!$cloud) throw new NotFoundEntityException('', '');
        return CloudViewModel::getDataCenterLocationsJson($cloud);
    }

    public function getCurrentDataCenterStaticMapForPDF()
    {
        $static_map_url = "http://maps.googleapis.com/maps/api/staticmap?zoom=1&size=300x200&maptype=roadmap";
        $instance_id = intval($this->request->param('ID'));
        $marketplace_type = $this->request->param('MARKETPLACETYPE');
        $query = new QueryObject();
        $query->addAndCondition(QueryCriteria::id('ID', $instance_id));
        switch (strtolower($marketplace_type)) {
            case 'public_cloud': {
                $cloud = $this->public_clouds_repository->getBy($query);
            }
                break;
            case 'private_cloud': {
                $cloud = $this->private_clouds_repository->getBy($query);
            }
                break;

        }

        if (!$cloud) throw new NotFoundEntityException('', '');
        $locations = json_decode(CloudViewModel::getDataCenterLocationsJson($cloud));

        foreach ($locations as $loc) {
            $static_map_url .= "&markers=" . $loc->lat . "," . $loc->lng;
        }

        return $static_map_url;
    }

    public function getCurrentDataCenterStaticMapDraftForPDF()
    {
        $static_map_url = "http://maps.googleapis.com/maps/api/staticmap?zoom=1&size=300x200&maptype=roadmap";
        $instance_id = intval($this->request->param('ID'));
        $marketplace_type = $this->request->param('MARKETPLACETYPE');
        $query = new QueryObject();
        $query->addAndCondition(QueryCriteria::id('ID', $instance_id));
        switch (strtolower($marketplace_type)) {
            case 'public_cloud': {
                $cloud = $this->public_clouds_draft_repository->getBy($query);
            }
                break;
            case 'private_cloud': {
                $cloud = $this->private_clouds_draft_repository->getBy($query);
            }
                break;

        }

        if (!$cloud) throw new NotFoundEntityException('', '');
        $locations = json_decode(CloudViewModel::getDataCenterLocationsJson($cloud));

        foreach ($locations as $loc) {
            $static_map_url .= "&markers=" . $loc->lat . "," . $loc->lng;
        }

        return $static_map_url;
    }

    public function getPricingSchemas()
    {
        return CloudViewModel::getPricingSchemas();
    }

    public function getPricingSchemasForPDF()
    {
        $pricing_schemas = CloudViewModel::getPricingSchemas();
        $enabled_ps = json_decode($this->getEnabledPricingSchemas());

        foreach ($pricing_schemas as $ps) {
            $ps->Enabled = 0;
            foreach ($enabled_ps as $eps) {
                if ($ps->ID == $eps) {
                    $ps->Enabled = 1;
                }
            }
        }

        return $pricing_schemas;
    }

    public function getPricingSchemasDraftForPDF()
    {
        $pricing_schemas = CloudViewModel::getPricingSchemas();
        $enabled_ps = json_decode($this->getEnabledPricingSchemasDraft());

        foreach ($pricing_schemas as $ps) {
            $ps->Enabled = 0;
            foreach ($enabled_ps as $eps) {
                if ($ps->ID == $eps) {
                    $ps->Enabled = 1;
                }
            }
        }

        return $pricing_schemas;
    }

    public function getEnabledPricingSchemas()
    {
        $instance_id = intval($this->request->param('ID'));
        $marketplace_type = $this->request->param('MARKETPLACETYPE');
        $query = new QueryObject();
        $query->addAndCondition(QueryCriteria::id('ID', $instance_id));
        switch (strtolower($marketplace_type)) {
            case 'public_cloud': {
                $cloud = $this->public_clouds_repository->getBy($query);
            }
                break;
            case 'private_cloud': {
                $cloud = $this->private_clouds_repository->getBy($query);
            }
                break;

        }
        if (!$cloud) throw new NotFoundEntityException('', '');
        return CloudViewModel::getEnabledPricingSchemas($cloud);
    }

    public function getEnabledPricingSchemasDraft()
    {
        $instance_id = intval($this->request->param('ID'));
        $marketplace_type = $this->request->param('MARKETPLACETYPE');
        $query = new QueryObject();
        $query->addAndCondition(QueryCriteria::id('ID', $instance_id));
        switch (strtolower($marketplace_type)) {
            case 'public_cloud': {
                $cloud = $this->public_clouds_draft_repository->getBy($query);
            }
                break;
            case 'private_cloud': {
                $cloud = $this->private_clouds_draft_repository->getBy($query);
            }
                break;

        }
        if (!$cloud) throw new NotFoundEntityException('', '');
        return CloudViewModel::getEnabledPricingSchemas($cloud);
    }

    public function getCurrentOfficesLocationsJson()
    {
        $instance_id = intval($this->request->param('ID'));
        $query = new QueryObject();
        $query->addAndCondition(QueryCriteria::id('ID', $instance_id));

        $consultant = $this->consultant_repository->getBy($query);

        if (!$consultant) throw new NotFoundEntityException('', '');
        return ConsultantViewModel::getOfficesLocationsJson($consultant);
    }

    public function getCurrentOfficesLocationsDraftJson()
    {
        $instance_id = intval($this->request->param('ID'));
        $query = new QueryObject();
        $query->addAndCondition(QueryCriteria::id('ID', $instance_id));

        $consultant = $this->consultant_draft_repository->getBy($query);

        if (!$consultant) throw new NotFoundEntityException('', '');
        return ConsultantViewModel::getOfficesLocationsJson($consultant);
    }

    public function getCurrentOfficesLocationsStaticMapForPDF()
    {
        $static_map_url = "http://maps.googleapis.com/maps/api/staticmap?zoom=1&size=300x200&maptype=roadmap";
        $instance_id = intval($this->request->param('ID'));
        $query = new QueryObject();
        $query->addAndCondition(QueryCriteria::id('ID', $instance_id));

        $consultant = $this->consultant_repository->getBy($query);

        if (!$consultant) throw new NotFoundEntityException('', '');
        $locations = json_decode(ConsultantViewModel::getOfficesLocationsJson($consultant));

        foreach ($locations as $loc) {
            $static_map_url .= "&markers=" . $loc->lat . "," . $loc->lng;
        }

        return $static_map_url;
    }

    public function getCurrentOfficesLocationsStaticMapDraftForPDF()
    {
        $static_map_url = "http://maps.googleapis.com/maps/api/staticmap?zoom=1&size=300x200&maptype=roadmap";
        $instance_id = intval($this->request->param('ID'));
        $query = new QueryObject();
        $query->addAndCondition(QueryCriteria::id('ID', $instance_id));

        $consultant = $this->consultant_draft_repository->getBy($query);

        if (!$consultant) throw new NotFoundEntityException('', '');
        $locations = json_decode(ConsultantViewModel::getOfficesLocationsJson($consultant));

        foreach ($locations as $loc) {
            $static_map_url .= "&markers=" . $loc->lat . "," . $loc->lng;
        }

        return $static_map_url;
    }

    public function pdf()
    {
        $html_inner = '';
        $marketplace_type = $this->request->param('MARKETPLACETYPE');
        $instance_id = intval($this->request->param('ID'));
        $base = Director::protocolAndHost();
        $query = new QueryObject();
        $query->addAndCondition(QueryCriteria::id('ID', $instance_id));

        switch (strtolower($marketplace_type)) {
            case 'distribution': {
                $distribution = $this->distribution_repository->getBy($query);
                if (!$distribution) throw new NotFoundEntityException('', '');
                $render = new DistributionSapphireRender($distribution);
                $distribution->IsPreview = true;
                $html_inner = $render->pdf();
                $css = @file_get_contents($base . "/marketplace/code/ui/admin/css/pdf.css");
            }
                break;
            case 'appliance': {
                $appliance = $this->appliance_repository->getBy($query);
                $appliance->IsPreview = true;
                $render = new ApplianceSapphireRender($appliance);
                $html_inner = $render->pdf();
                $css = @file_get_contents($base . "/marketplace/code/ui/admin/css/pdf.css");
            }
                break;
            case 'public_cloud': {
                $public_cloud = $this->public_clouds_repository->getBy($query);
                $public_cloud->IsPreview = true;
                if (!$public_cloud) throw new NotFoundEntityException('', '');
                $render = new PublicCloudSapphireRender($public_cloud);
                $html_inner = $render->pdf();
                $css = @file_get_contents($base . "/marketplace/code/ui/admin/css/pdf.css");
            }
                break;
            case 'private_cloud': {
                $private_cloud = $this->private_clouds_repository->getBy($query);
                $private_cloud->IsPreview = true;
                $render = new PrivateCloudSapphireRender($private_cloud);
                $html_inner = $render->pdf();
                $css = @file_get_contents($base . "/marketplace/code/ui/admin/css/pdf.css");

            }
                break;
            case 'consultant': {
                $consultant = $this->consultant_repository->getBy($query);
                if (!$consultant) throw new NotFoundEntityException('', '');
                $consultant->IsPreview = true;
                $render = new ConsultantSapphireRender($consultant);
                $html_inner = $render->pdf();
                $css = @file_get_contents($base . "/marketplace/code/ui/admin/css/pdf.css");
            }
                break;
            default:
                $this->httpError(404);
                break;
        }

        //create pdf
        $file = FileUtils::convertToFileName('preview') . '.pdf';

        $html_outer = sprintf("<html><head><style>%s</style></head><body><div class='container'>%s</div></body></html>",
            str_replace("@host", $base, $css), $html_inner);


        try {
            $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8', array(15, 5, 15, 5));
            $html2pdf->setTestIsImage(false);
            //$html2pdf->addFont('Open Sans', '', $base.'/themes/openstack/assets/fonts/PT-Sans/PTC75F-webfont.ttf');
            $html2pdf->WriteHTML($html_outer);
            //clean output buffer
            ob_end_clean();
            $html2pdf->Output($file, "D");
        } catch (HTML2PDF_exception $e) {
            $message = array(
                'errno' => '',
                'errstr' => $e->__toString(),
                'errfile' => 'MarketPlaceAdminPage.php',
                'errline' => '',
                'errcontext' => ''
            );
            SS_Log::log($message, SS_Log::ERR);
            $this->httpError(404, 'There was an error on PDF generation!');
        }
    }

    public function draft_pdf()
    {
        $html_inner = '';
        $marketplace_type = $this->request->param('MARKETPLACETYPE');
        $instance_id = intval($this->request->param('ID'));
        $base = Director::baseFolder();

        $query = new QueryObject();
        $query->addAndCondition(QueryCriteria::id('ID', $instance_id));

        switch (strtolower($marketplace_type)) {
            case 'distribution': {
                $distribution = $this->distribution_draft_repository->getBy($query);
                if (!$distribution) throw new NotFoundEntityException('', '');
                $render = new DistributionSapphireRender($distribution);
                $distribution->IsPreview = true;
                $html_inner = $render->pdf();
                $css = @file_get_contents($base . "/marketplace/code/ui/admin/css/pdf.css");
            }
                break;
            case 'appliance': {
                $appliance = $this->appliance_draft_repository->getBy($query);
                $appliance->IsPreview = true;
                $render = new ApplianceSapphireRender($appliance);
                $html_inner = $render->pdf();
                $css = @file_get_contents($base . "/marketplace/code/ui/admin/css/pdf.css");
            }
                break;
            case 'public_cloud': {
                $public_cloud = $this->public_clouds_draft_repository->getBy($query);
                $public_cloud->IsPreview = true;
                if (!$public_cloud) throw new NotFoundEntityException('', '');
                $render = new PublicCloudSapphireRender($public_cloud);
                $html_inner = $render->pdf();
                $css = @file_get_contents($base . "/marketplace/code/ui/admin/css/pdf.css");
            }
                break;
            case 'private_cloud': {
                $private_cloud = $this->private_clouds_draft_repository->getBy($query);
                $private_cloud->IsPreview = true;
                $render = new PrivateCloudSapphireRender($private_cloud);
                $html_inner = $render->pdf();
                $css = @file_get_contents($base . "/marketplace/code/ui/admin/css/pdf.css");

            }
                break;
            case 'consultant': {
                $consultant = $this->consultant_draft_repository->getBy($query);
                if (!$consultant) throw new NotFoundEntityException('', '');
                $consultant->IsPreview = true;
                $render = new ConsultantSapphireRender($consultant);
                $html_inner = $render->pdf();
                $css = @file_get_contents($base . "/marketplace/code/ui/admin/css/pdf.css");
            }
                break;
            default:
                $this->httpError(404);
                break;
        }

        //create pdf
        $file = FileUtils::convertToFileName('preview') . '.pdf';

        $html_outer = sprintf("<html><head><style>%s</style></head><body><div class='container'>%s</div></body></html>",
            str_replace("@host", $base, $css), $html_inner);


        try {
            $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8', array(15, 5, 15, 5));
            //$html2pdf->addFont('Open Sans', '', $base.'/themes/openstack/assets/fonts/PT-Sans/PTC75F-webfont.ttf');
            $html2pdf->setTestIsImage(false);
            $html2pdf->WriteHTML($html_outer);
            //clean output buffer
            ob_end_clean();
            $html2pdf->Output($file, "D");
        } catch (HTML2PDF_exception $e) {
            $message = array(
                'errno' => '',
                'errstr' => $e->__toString(),
                'errfile' => 'UserStory.php',
                'errline' => '',
                'errcontext' => ''
            );
            SS_Log::log($message, SS_Log::ERR);
            $this->httpError(404, 'There was an error on PDF generation!');
        }
    }

    /**
     *  interop
     */


    public function getInteropProgramVersions()
    {
        list($res, $size) = $this->interop_program_repository->getAllOrdered();
        return new ArrayList($res);
    }

}