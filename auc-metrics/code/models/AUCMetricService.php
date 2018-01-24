<?php
/**
 * Copyright 2018 OpenStack Foundation
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
 * Class AUCMetricService
 */
final class AUCMetricService implements IAUCMetricService
{

    /**
     * @var IAUCMetricMissMatchErrorRepository
     */
    private $repository;

    /**
     * @var IMemberRepository
     */
    private $member_repository;

    /**
     * @var ITransactionManager
     */
    private $tx_service;

    /**
     * AUCMetricService constructor.
     * @param IAUCMetricMissMatchErrorRepository $repository
     * @param IMemberRepository $member_repository
     * @param ITransactionManager $tx_service
     */
    public function __construct
    (
        IAUCMetricMissMatchErrorRepository $repository,
        IMemberRepository $member_repository,
        ITransactionManager $tx_service
    )
    {
        $this->repository        = $repository;
        $this->member_repository = $member_repository;
        $this->tx_service        = $tx_service;
    }


    /**
     * @param int $error_id
     * @param int $member_id
     * @return mixed
     * @throws NotFoundEntityException
     */
    public function fixMissMatchUserError($error_id, $member_id)
    {
        return $this->tx_service->transaction(function () use ($error_id, $member_id){

            $error = $this->repository->getById($error_id);
            if(is_null($error))
                throw new NotFoundEntityException();

            $member = $this->member_repository->getById($member_id);
            if(is_null($member))
                throw new NotFoundEntityException();

            $trans = new AUCMetricTranslation;
            $trans->UserIdentifier           = $error->UserIdentifier;
            $trans->MappedFoundationMemberID = $member->ID;
            $trans->CreatorID                = Member::currentUserID();
            $trans->write();

            $error->markAsSolved();
            $error->write();

            return $trans;
        });
    }

    /**
     * @param int $error_id
     * @throws NotFoundEntityException
     * @return mixed
     */
    public function deleteMissMatchUserError($error_id)
    {
        return $this->tx_service->transaction(function () use ($error_id){

            $error = $this->repository->getById($error_id);
            if(is_null($error))
                throw new NotFoundEntityException();
            $this->repository->delete($error);
        });
    }
}