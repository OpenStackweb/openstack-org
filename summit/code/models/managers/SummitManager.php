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
 * Class SummitManager
 */
final class SummitManager {

	/**
	 * @var IEntityRepository
	 */
	private $summit_repository;
    /**
     * @var ISummitFactory
     */
    private $summit_factory;
    /**
     * @var IEntityRepository
     */
    private $summittype_repository;
    /**
     * @var ISummitTypeFactory
     */
    private $summittype_factory;
	/**
	 * @var ITransactionManager
	 */
	private $tx_manager;

	/**
	 * @param IEntityRepository   $summit_repository
	 * @param ISummitFactory      $summit_factory
	 * @param ITransactionManager $tx_manager
	 */
	public function __construct(IEntityRepository $summit_repository,
                                ISummitFactory $summit_factory,
                                IEntityRepository $summittype_repository,
                                ISummitTypeFactory $summittype_factory,
	                            ITransactionManager $tx_manager){
		$this->summit_repository = $summit_repository;
		$this->summit_factory    = $summit_factory;
        $this->summittype_repository = $summittype_repository;
        $this->summittype_factory    = $summittype_factory;
        $this->tx_manager        = $tx_manager;
	}

    /**
     * @param array $data
     * @return ISummit
     */
    public function createSummit(array $data){

        $this_var           = $this;
        $repository         = $this->summit_repository;
        $factory            = $this->summit_factory;

        return  $this->tx_manager->transaction(function() use ($this_var, $factory, $data, $repository){
            $summit = new Summit();
            $summit->registerMainInfo($factory->buildMainInfo($data));

            if ($repository->isDuplicated($summit)) {
                throw new EntityAlreadyExistsException('Summit',sprintf('Name %s',$summit->getName()));
            }

            $repository->add($summit);
            return $summit;
        });
    }

    /**
     * @param $id
     * @return ISummit
     */
    public function deleteSummit($id){
        $repository = $this->summit_repository;

        $summit =  $this->tx_manager->transaction(function() use ($id, $repository){
            $summit = $repository->getById($id);
            if(!$summit)
                throw new NotFoundEntityException('Summit',sprintf('id %s',$id ));

            $repository->delete($summit);
        });
    }

    /**
     * @param $summit_id
     * @param array $data
     * @return ISummitType
     */
    public function saveSummitType($summit_id, array $data){

        $this_var              = $this;
        $summittype_repository  = $this->summittype_repository;
        $summit_repository     = $this->summit_repository;
        $factory               = $this->summittype_factory;

        return  $this->tx_manager->transaction(function() use ($this_var, $factory, $summit_id, $data, $summit_repository, $summittype_repository){

            $summit = $summit_repository->getById($summit_id);
            if(!$summit)
                throw new NotFoundEntityException('Summit',sprintf('id %s',$summit_id ));

            if (isset($data['summittype_id'])) {
                $summittype = $summittype_repository->getById(intval($data['summittype_id']));
                $summittype->setTitle($data['title']);
                $summittype->setDescription($data['description']);
                $summittype->setAudience($data['audience']);
                $summittype->setStartDate($data['start_date']);
                $summittype->setEndDate($data['end_date']);

            } else {
                $summittype = $factory->buildSummitType($data,$summit_id);
                $summittype_repository->add($summittype);
            }

            return $summittype;
        });
    }

} 