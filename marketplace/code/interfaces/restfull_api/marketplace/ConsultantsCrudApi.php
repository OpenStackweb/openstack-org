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
 * Class ConsultantsCrudApi
 */
final class ConsultantsCrudApi extends CompanyServiceCrudApi
{

    /**
     * @var array
     */
    static $url_handlers = array(
        'GET languages' => 'getLanguages',
        'GET $COMPANY_SERVICE_ID!' => 'getConsultant',
        'DELETE $COMPANY_SERVICE_ID!/$IS_DRAFT!' => 'deleteCompanyService',
        'POST ' => 'addCompanyService',
        'PUT ' => 'updateCompanyService',
        'PUT $COMPANY_SERVICE_ID!' => 'publishCompanyService',
    );

    /**
     * @var array
     */
    static $allowed_actions = array(
        'getConsultant',
        'deleteCompanyService',
        'addCompanyService',
        'updateCompanyService',
        'getLanguages',
        'publishCompanyService'
    );

    /**
     * @var IEntityRepository
     */
    private $consultant_repository;
    private $consultant_draft_repository;
    /**
     * @var IEntityRepository
     */
    private $languages_repository;

    public function __construct()
    {

        $this->consultant_repository = new SapphireConsultantRepository;
        $this->consultant_draft_repository = new SapphireConsultantRepository(true);
        $this->marketplace_type_repository = new SapphireMarketPlaceTypeRepository;
        $this->languages_repository = new SapphireSpokenLanguageRepository;

        $manager = new ConsultantManager (
            $this->consultant_repository,
            new SapphireMarketPlaceVideoTypeRepository,
            $this->marketplace_type_repository,
            new SapphireOpenStackApiVersionRepository,
            new SapphireOpenStackComponentRepository,
            new SapphireOpenStackReleaseRepository,
            new SapphireRegionRepository,
            new SapphireSupportChannelTypeRepository,
            $this->languages_repository,
            new SapphireConfigurationManagementTypeRepository,
            new SapphireConsultantServiceOfferedTypeRepository,
            new ConsultantAddPolicy($this->consultant_repository, $this->marketplace_type_repository),
            new CompanyServiceCanAddResourcePolicy,
            new CompanyServiceCanAddVideoPolicy,
            new ConsultantFactory,
            new MarketplaceFactory,
            new ValidatorFactory,
            new OpenStackApiFactory,
            new GoogleGeoCodingService(
                new SapphireGeoCodingQueryRepository,
                new UtilFactory,
                SapphireTransactionManager::getInstance()
            ),
            null,
            new SessionCacheService,
            SapphireTransactionManager::getInstance()
        );

        $draft_manager = new ConsultantManager (
            $this->consultant_draft_repository,
            new SapphireMarketPlaceVideoTypeRepository,
            $this->marketplace_type_repository,
            new SapphireOpenStackApiVersionRepository,
            new SapphireOpenStackComponentRepository,
            new SapphireOpenStackReleaseRepository,
            new SapphireRegionRepository,
            new SapphireSupportChannelTypeRepository,
            $this->languages_repository,
            new SapphireConfigurationManagementTypeRepository,
            new SapphireConsultantServiceOfferedTypeRepository,
            new ConsultantAddPolicy($this->consultant_draft_repository, $this->marketplace_type_repository),
            new CompanyServiceCanAddResourcePolicy,
            new CompanyServiceCanAddVideoPolicy,
            new ConsultantDraftFactory,
            new MarketplaceDraftFactory,
            new ValidatorFactory,
            new OpenStackApiFactory,
            new GoogleGeoCodingService(
                new SapphireGeoCodingQueryRepository,
                new UtilFactory,
                SapphireTransactionManager::getInstance()
            ),
            null,
            new SessionCacheService,
            SapphireTransactionManager::getInstance()
        );

        parent::__construct($manager, $draft_manager, new ConsultantFactory, new ConsultantDraftFactory);

        // filters ...
        $this_var = $this;
        $current_user = $this->current_user;
        $repository = $this->consultant_repository;
        $draft_repository = $this->consultant_draft_repository;

        $this->addBeforeFilter('addCompanyService', 'check_add_company', function ($request) use ($this_var, $current_user) {
            $data = $this_var->getJsonRequest();
            if (!$data) return $this->serverError();
            $company_id = intval(@$data['company_id']);
            if (!$current_user->isMarketPlaceAdminOfCompany(IConsultant::MarketPlaceGroupSlug, $company_id))
                return $this_var->permissionFailure();
        });

        $this->addBeforeFilter('updateCompanyService', 'check_update_company', function ($request) use ($this_var, $current_user) {
            $data = $this_var->getJsonRequest();
            if (!$data) return $this->serverError();
            if (!$current_user->isMarketPlaceAdminOfCompany(IConsultant::MarketPlaceGroupSlug, intval(@$data['company_id'])))
                return $this_var->permissionFailure();
        });

        $this->addBeforeFilter('deleteCompanyService', 'check_delete_company', function ($request) use ($this_var, $current_user, $repository, $draft_repository) {
            $company_service_id = intval($request->param('COMPANY_SERVICE_ID'));
            $is_draft = intval($this->request->param('IS_DRAFT'));
            $company_service = ($is_draft) ? $draft_repository->getById($company_service_id) : $repository->getById($company_service_id);

            if (!$current_user->isMarketPlaceAdminOfCompany(IConsultant::MarketPlaceGroupSlug, $company_service->getCompany()->getIdentifier()))
                return $this_var->permissionFailure();
        });
    }


    public function getConsultant()
    {
        $company_service_id = intval($this->request->param('COMPANY_SERVICE_ID'));
        $consultant = $this->consultant_repository->getById($company_service_id);
        if (!$consultant)
            return $this->notFound();
        return $this->ok(ConsultantAssembler::convertConsultantToArray($consultant));
    }

    public function getConsultantDraft()
    {
        $company_service_id = intval($this->request->param('COMPANY_SERVICE_ID'));
        $consultant = $this->consultant_draft_repository->getByLiveServiceId($company_service_id);
        if (!$consultant)
            return $this->notFound();
        return $this->ok(OpenStackImplementationAssembler::convertOpenStackImplementationToArray($consultant));
    }

    public function getLanguages()
    {
        $term = Convert::raw2sql($this->request->getVar('term'));
        $query = new QueryObject;
        $query->addAndCondition(QueryCriteria::like('Name', $term));
        list($list, $size) = $this->languages_repository->getAll($query, 0, 20);
        $res = array();
        foreach ($list as $lang) {
            array_push($res, array('label' => $lang->getName(), 'value' => $lang->getName()));
        }
        return $this->ok($res);
    }

    public function addCompanyService()
    {
        try {
            return parent::addCompanyServiceDraft();
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function updateCompanyService()
    {
        try {
            return parent::updateCompanyServiceDraft();
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function publishCompanyService()
    {
        try {
            return parent::publishCompanyService();
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function deleteCompanyService()
    {
        try {
            $company_service_id = intval($this->request->param('COMPANY_SERVICE_ID'));
            $is_draft = intval($this->request->param('IS_DRAFT'));

            if ($is_draft) {
                $this->draft_manager->unRegister($this->draft_factory->buildCompanyServiceById($company_service_id));
            } else {
                $this->manager->unRegister($this->factory->buildCompanyServiceById($company_service_id));
                $company_service_draft = $this->consultant_draft_repository->getByLiveServiceId($company_service_id);
                if ($company_service_draft) {
                    $this->draft_manager->unRegister($company_service_draft);
                }
            }

            return $this->deleted();
        } catch (NotFoundEntityException $ex1) {
            SS_Log::log($ex1, SS_Log::ERR);
            return $this->notFound($ex1->getMessage());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

}