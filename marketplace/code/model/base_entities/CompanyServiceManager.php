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
 * Class CompanyServiceManager
 */
abstract class CompanyServiceManager
{

    /**
     * @var ITransactionManager
     */
    protected $tx_manager;

    /**
     * @var ICompanyServiceRepository
     */
    protected $repository;

    /**
     * @var IMarketPlaceTypeAddPolicy
     */
    protected $add_policy;

    /**
     * @var ICompanyServiceCanAddResourcePolicy
     */
    protected $add_resource_policy;

    /**
     * @var ICompanyServiceCanAddVideoPolicy
     */
    protected $add_video_policy;

    /**
     * @var IEntityRepository
     */
    protected $video_type_repository;

    /**
     * @var IValidatorFactory
     */
    protected $validator_factory;


    /**
     * @var IMarketplaceFactory
     */
    protected $marketplace_factory;

    /**
     * @var IMarketplaceTypeRepository
     */
    protected $marketplace_type_repository;

    /**
     * @var ICompanyServiceFactory
     */
    protected $factory;

    /**
     * @var IMarketPlaceTypeCanShowInstancePolicy
     */
    protected $show_policy;
    /**
     * @var ICacheService
     */
    protected $cache_service;

    /**
     * @param IEntityRepository $repository
     * @param IEntityRepository|null $video_type_repository
     * @param IMarketplaceTypeRepository $marketplace_type_repository
     * @param IMarketPlaceTypeAddPolicy $add_policy
     * @param ICompanyServiceCanAddResourcePolicy $add_resource_policy
     * @param ICompanyServiceCanAddVideoPolicy $add_video_policy
     * @param ICompanyServiceFactory $factory
     * @param IMarketplaceFactory $marketplace_factory
     * @param IValidatorFactory $validator_factory
     * @param IMarketPlaceTypeCanShowInstancePolicy $show_policy
     * @param ICacheService $cache_service
     * @param ITransactionManager $tx_manager
     */
    public function __construct(
        ?IEntityRepository $repository,
        ?IEntityRepository $video_type_repository,
        ?IMarketplaceTypeRepository $marketplace_type_repository,
        ?IMarketPlaceTypeAddPolicy $add_policy,
        ?ICompanyServiceCanAddResourcePolicy $add_resource_policy,
        ?ICompanyServiceCanAddVideoPolicy $add_video_policy,
        ?ICompanyServiceFactory $factory,
        ?IMarketplaceFactory $marketplace_factory,
        ?IValidatorFactory $validator_factory,
        ?IMarketPlaceTypeCanShowInstancePolicy $show_policy,
        ?ICacheService $cache_service,
        ITransactionManager $tx_manager
    ) {

        $this->repository = $repository;
        $this->video_type_repository = $video_type_repository;
        $this->marketplace_type_repository = $marketplace_type_repository;
        $this->add_policy = $add_policy;
        $this->tx_manager = $tx_manager;
        $this->add_resource_policy = $add_resource_policy;
        $this->add_video_policy = $add_video_policy;
        $this->marketplace_factory = $marketplace_factory;
        $this->factory = $factory;
        $this->validator_factory = $validator_factory;
        $this->show_policy = $show_policy;
        $this->cache_service = $cache_service;
    }

    public function unRegister(ICompanyService $company_service)
    {
        $repository = $this->repository;
        $this->tx_manager->transaction(function () use ($repository, $company_service) {
            $company_id = $company_service->getIdentifier();
            $company_service = $repository->getById($company_id);
            if (!$company_service) {
                throw new NotFoundEntityException('ICompanyService', sprintf("id %s", $company_id));
            }
            $repository->delete($company_service);
        });
    }


    /**
     * @param ICompanyService $company_service
     * @throws EntityAlreadyExistsException
     * @throws PolicyException
     * @return int
     */
    public function register(ICompanyService &$company_service)
    {

        $repository = $this->repository;

        if (!is_null($this->add_policy)) {
            $this->add_policy->canAdd($company_service->getCompany());
        }

        $query = new QueryObject($company_service);
        $query->addAndCondition(QueryCriteria::equal('Name', $company_service->getName()));
        $query->addAndCondition(QueryCriteria::equal('Company.ID', $company_service->getCompany()->getIdentifier()));
        $res = $repository->getBy($query);
        if ($res) {
            throw new EntityAlreadyExistsException('CompanyService', sprintf('name %s', $company_service->getName()));
        }

        return $repository->add($company_service);
    }

    /**
     * @return IMarketPlaceType
     * @throws NotFoundEntityException
     */
    abstract protected function getMarketPlaceType();

    /**
     * @param array $data
     * @return ICompanyService
     * @throws EntityValidationException
     * @throws EntityAlreadyExistsException
     * @throws NotFoundEntityException
     */
    public function addCompanyService(array $data)
    {
        $marketplace_factory = $this->marketplace_factory;
        $factory            = $this->factory;
        $validator_factory = $this->validator_factory;

        return $this->tx_manager->transaction(function () use (
            $marketplace_factory,
            $data,
            $factory,
            $validator_factory
        ) {

            $validator = $validator_factory->buildValidatorForCompanyService($data);

            if ($validator->fails()) {
                return $this->validationError($validator->messages());
            }

            $company = $marketplace_factory->buildCompanyById(intval($data['company_id']));
            $live_service_id = (isset($data['live_service_id'])) ? $data['live_service_id'] : null;
            $company_service = $this->buildCompanyService($data, $company, $live_service_id);
            $this->register($company_service);

            $this->updateCollections($company_service, $data);

            return $company_service;
        });

    }

    public function buildCompanyService($data, $company, $live_service_id)
    {
        $company_service = $this->factory->buildCompanyService(
            $data['name'],
            $data['overview'],
            $company,
            $data['active'],
            $this->getMarketPlaceType(),
            $data['call_2_action_uri'],
            $live_service_id,
            $data['published']);

        return $company_service;
    }

    protected function updateCollections(ICompanyService $company_service, array $data)
    {
        // resources
        if (array_key_exists('additional_resources', $data) && is_array($data['additional_resources'])) {
            $data_resources = $data['additional_resources'];
            foreach ($data_resources as $data_resource) {
                $this->registerCompanyServiceResource($data_resource, $company_service);
            }
        }
        // videos
        if (array_key_exists('videos', $data) && is_array($data['videos'])) {
            $videos = $data['videos'];
            foreach ($videos as $video_data) {
                $this->registerCompanyServiceVideo($video_data, $company_service);
            }
        }
        return $company_service;
    }

    protected function clearCollections(ICompanyService $company_service)
    {
        $company_service->clearVideos();
        $company_service->clearResources();
        return $company_service;
    }

    /**
     * @param array $data
     * @return IEntity|void
     * @throws EntityAlreadyExistsException
     * @throws NotFoundEntityException
     */
    public function updateCompanyService(array $data)
    {

        $validator_factory = $this->validator_factory;
        $repository = $this->repository;
        $marketplace_factory = $this->marketplace_factory;

        $company_service = $this->tx_manager->transaction(function () use (
            &$company_service,
            $marketplace_factory,
            $data,
            $validator_factory,
            $repository
        ) {
            $validator = $validator_factory->buildValidatorForCompanyService($data);
            if ($validator->fails()) {
                return $this->validationError($validator->messages());
            }
            $id = intval($data['id']);
            $company_service = $repository->getById($id);
            if (!$company_service) {
                throw new NotFoundEntityException('CompanyService', sprintf("id %s", $id));
            }
            $company_service->setName($data['name']);
            if ($company_service->isDraft()) {
                $live_service_id = (isset($data['live_service_id'])) ? $data['live_service_id'] : 0;
                $published = (isset($data['published'])) ? $data['published'] : 0;
                $company_service->setPublished($published);
                $company_service->setLiveServiceId($live_service_id);
            }

            $query = new QueryObject($company_service);
            $query->addAndCondition(QueryCriteria::equal('Name', $company_service->getName()));
            $query->addAndCondition(QueryCriteria::equal('Company.ID',
                $company_service->getCompany()->getIdentifier()));
            $query->addAndCondition(QueryCriteria::notId('ID', $id));

            $res = $repository->getBy($query);
            if ($res) {
                throw new EntityAlreadyExistsException('CompanyService',
                    sprintf('name %s', $company_service->getName()));
            }


            $this->update($company_service, $data);

            $this->clearCollections($company_service);

            $this->updateCollections($company_service, $data);

            // store updates
            $update_record = new CompanyServiceUpdateRecord();
            $update_record->storeUpdate($company_service);

            return $company_service;
        });

        return $company_service;
    }

    public function update($company_service, $data)
    {
        $company_service->setOverview($data['overview']);
        if ($data['active']) {
            $company_service->activate();
        } else {
            $company_service->deactivate();
        }
        $company_service->setCall2ActionUri($data['call_2_action_uri']);
        $company_service->setCompany($this->marketplace_factory->buildCompanyById(intval($data['company_id'])));

        return $company_service;
    }

    protected function registerCompanyServiceVideo(array $data, ICompanyService $company_service)
    {
        $validator = $this->validator_factory->buildValidatorForMarketPlaceVideo($data);
        if ($validator->fails()) {
            return $this->validationError($validator->messages());
        }
        $video = $this->marketplace_factory->buildVideo(
            $data['title'],
            isset($data['description']) ? $data['description'] : '',
            $data['youtube_id'],
            intval($data['length']),
            $this->marketplace_factory->buildVideoTypeById(intval($data['type_id'])),
            $company_service);

        $company_service_id = $video->getOwner()->getIdentifier();
        $company_service = $video->getOwner();
        if ($company_service_id > 0 && !$this->repository->getById($company_service_id)) {
            throw new NotFoundEntityException('CompanyService', sprintf("id %s", $company_service_id));
        }
        $video_type_id = $video->getType()->getIdentifier();
        $video_type = $this->video_type_repository->getById($video_type_id);
        if (!$video_type) {
            throw new NotFoundEntityException('MarketPlaceVideoType', sprintf("id %s", $video_type_id));
        }
        if (!is_null($this->add_video_policy)) {
            $this->add_video_policy->canAdd($company_service, $video_type);
        }
        $company_service->addVideo($video);
    }

    protected function registerCompanyServiceResource(array $data, ICompanyService $company_service)
    {
        $validator = $this->validator_factory->buildValidatorForCompanyResource($data);
        if ($validator->fails()) {
            return $this->validationError($validator->messages());
        }
        $resource = $this->marketplace_factory->buildResource($data['name'], $data['link'], $company_service);
        $company_service_id = $resource->getOwner()->getIdentifier();
        $company_service = $resource->getOwner();
        if ($company_service_id > 0 && !$this->repository->getById($company_service_id)) {
            throw new NotFoundEntityException('CompanyService', sprintf("id %s", $company_service_id));
        }
        if (!is_null($this->add_resource_policy)) {
            $this->add_resource_policy->canAdd($company_service);
        }
        $company_service->addResource($resource);

        return $resource->getIdentifier();
    }

    /**
     * @param array $messages
     * @throws EntityValidationException
     */
    protected function validationError(array $messages)
    {
        throw new EntityValidationException($messages);
    }

    /**
     * @param $current_date
     * @return null
     */
    public function getActives($current_date = null)
    {
        $services = array();
        $ordering_set = false;
        $prefix = get_class($this);
        $order = $this->cache_service->getSingleValue(strtolower($prefix . ".ordering"));
        if (!empty($order)) {
            $service_count = $this->repository->countActives();
            if (intval($service_count) != count(explode(',', $order))) {
                //select random order
                $services = $this->repository->getActivesRandom();
            } else {
                $ordering_set = true;
                $services = $this->repository->getActivesByList($order);
            }
        } else {
            $services = $this->repository->getActivesRandom();
        }

        if (count($services)) {

            $ordering = array();
            $to_remove = array();
            foreach ($services as $s) {
                if (!is_null($this->show_policy) && !$this->show_policy->canShow($s->getIdentifier())) {
                    array_push($to_remove, $s);
                }
                array_push($ordering, $s->getIdentifier());
            }

            $services = array_diff($services, $to_remove);

            if (!$ordering_set)//store random order for next time to maintain consistency
            {
                $this->cache_service->setSingleValue(strtolower($prefix . ".ordering"), implode(', ', $ordering));
            }
        }

        return $services;
    }

}
