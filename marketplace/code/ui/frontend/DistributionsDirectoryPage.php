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
class DistributionsDirectoryPage extends MarketPlaceDirectoryPage
{
    static $allowed_children = "none";
}


class DistributionsDirectoryPage_Controller extends MarketPlaceDirectoryPage_Controller
{

    private static $allowed_actions = array(
        'handleIndex',
    );

    static $url_handlers = array(
        '$Type!/$Company!/$Slug!' => 'handleIndex',
    );

    /**
     * @var IOpenStackImplementationRepository
     */
    private $distribution_repository;

    /**
     * @var IOpenStackImplementationRepository
     */
    private $appliance_repository;

    /**
     * @var ApplianceManager
     */
    private $appliance_manager;

    /**
     * @var DistributionManager
     */
    private $distribution_manager;


    /**
     * @var IQueryHandler
     */
    private $implementations_services_query;

    function init()
    {
        parent::init();

        Requirements::css(THIRDPARTY_DIR . '/jquery-ui-themes/smoothness/jquery-ui.css');
        Requirements::javascript(THIRDPARTY_DIR . '/jquery-ui/jquery-ui.js');

        Requirements::customScript("jQuery(document).ready(function($) {
            $('#distros','.marketplace-nav').addClass('current');
        });");
        Requirements::css("themes/openstack/css/chosen.css", "screen,projection");

        Requirements::combine_files('marketplace_distros_landing.js',
            array(
                "themes/openstack/javascript/chosen.jquery.min.js",
                "marketplace/code/ui/frontend/js/implementation.directory.page.js"
            ));

        Requirements::customScript($this->GATrackingCode());

        $this->distribution_repository = new SapphireDistributionRepository;
        $this->appliance_repository = new SapphireApplianceRepository;

        $this->appliance_manager = new ApplianceManager (
            $this->appliance_repository,
            new SapphireMarketPlaceVideoTypeRepository,
            new SapphireMarketPlaceTypeRepository,
            new SapphireGuestOSTypeRepository,
            new SapphireHyperVisorTypeRepository,
            new SapphireOpenStackApiVersionRepository,
            new SapphireOpenStackComponentRepository,
            new SapphireOpenStackReleaseRepository,
            new SapphireRegionRepository,
            new SapphireSupportChannelTypeRepository,
            new SapphireOpenStackReleaseSupportedApiVersionRepository,
            new ApplianceAddPolicy($this->appliance_repository, new SapphireMarketPlaceTypeRepository),
            new CompanyServiceCanAddResourcePolicy,
            new CompanyServiceCanAddVideoPolicy,
            new ApplianceFactory,
            new MarketplaceFactory,
            new ValidatorFactory,
            new OpenStackApiFactory,
            null,
            new SessionCacheService,
            SapphireTransactionManager::getInstance()
        );


        $this->distribution_manager = new DistributionManager (
            $this->distribution_repository,
            new SapphireMarketPlaceVideoTypeRepository,
            new SapphireMarketPlaceTypeRepository,
            new SapphireGuestOSTypeRepository,
            new SapphireHyperVisorTypeRepository,
            new SapphireOpenStackApiVersionRepository,
            new SapphireOpenStackComponentRepository,
            new SapphireOpenStackReleaseRepository,
            new SapphireRegionRepository,
            new SapphireSupportChannelTypeRepository,
            new SapphireOpenStackReleaseSupportedApiVersionRepository,
            new DistributionAddPolicy($this->distribution_repository, new SapphireMarketPlaceTypeRepository),
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

        $this->implementations_services_query = new OpenStackImplementationServicesQueryHandler;
    }


    public function handleIndex()
    {
        $params = $this->request->allParams();
        if (isset($params["Type"]) && isset($params["Company"]) && isset($params["Slug"])) {
            //render instance ...
            if ($params["Type"] == 'distribution') {
                return $this->distribution();
            } else {
                return $this->appliance();
            }
        }
    }


    public function getImplementations()
    {
        $list1 = $this->distribution_manager->getActives();
        $list2 = $this->appliance_manager->getActives();

        //return on view model
        return new ArrayList(array_merge($list1, $list2));
    }

    public function distribution()
    {
        try {
            $params = $this->request->allParams();
            $company_url_segment = Convert::raw2sql($params["Company"]);
            $slug = Convert::raw2sql($params["Slug"]);
            $query = new QueryObject();
            $query->addAndCondition(QueryCriteria::equal('Slug', $slug));
            $distribution = $this->distribution_repository->getBy($query);
            if (!$distribution || !$distribution->Active) {
                throw new NotFoundEntityException('', '');
            }
            if ($distribution->getCompany()->URLSegment != $company_url_segment) {
                throw new NotFoundEntityException('', '');
            }
            // we need this for reviews.
            $this->company_service_ID = $distribution->getIdentifier();
            $render = new DistributionSapphireRender($distribution);

            return $render->draw();
        } catch (Exception $ex) {
            SS_Log::log($ex->getMessage(), SS_Log::WARN);
            return $this->httpError(404, 'Sorry that Distribution could not be found!.');
        }
    }

    public function appliance()
    {
        try {
            $params = $this->request->allParams();
            $company_url_segment = Convert::raw2sql($params["ID"]);
            $slug = Convert::raw2sql($params["Slug"]);
            $query = new QueryObject();
            $query->addAndCondition(QueryCriteria::equal('Slug', $slug));
            $appliance = $this->appliance_repository->getBy($query);
            if (!$appliance || !$appliance->Active) {
                throw new NotFoundEntityException('', '');
            }
            $this->company_service_ID = $appliance->getIdentifier();
            if ($appliance->getCompany()->URLSegment != $company_url_segment) {
                throw new NotFoundEntityException('', '');
            }
            $render = new ApplianceSapphireRender($appliance);

            return $render->draw();
        } catch (Exception $ex) {
            SS_Log::log($ex->getMessage(), SS_Log::WARN);
            return $this->httpError(404, 'Sorry that Appliance could not be found!.');
        }
    }

    public function ServicesCombo()
    {
        $source = array();
        $result = $this->implementations_services_query->handle(new OpenStackImplementationNamesQuerySpecification(''));
        foreach ($result->getResult() as $dto) {
            $source[$dto->getValue()] = $dto->getValue();
        }

        $ddl = new DropdownField('service-term"', $title = null, $source);
        $ddl->setEmptyString('-- Show All --');

        return $ddl;
    }
}