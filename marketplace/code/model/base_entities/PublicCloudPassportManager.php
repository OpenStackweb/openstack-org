<?php

/**
 * Copyright 2017 OpenStack Foundation
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
final class PublicCloudPassportManager implements IPublicCloudPassportManager
{
    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * @var IPublicCloudPassportRepository
     */
    private $repository;

    /**
     * PublicCloudPassportManager constructor.
     * @param IPublicCloudPassportRepository $repository
     * @param ITransactionManager $tx_manager
     */
    public function __construct
    (
        IOpenStackPoweredServiceRepository $repository,
        ITransactionManager $tx_manager
    )
    {
        $this->tx_manager = $tx_manager;
        $this->repository = $repository;
    }

    /**
     * @param array $data
     * @param int $service_id
     */
    public function updatePublicCloudPassport(array $data, $service_id){

        $this->tx_manager->transaction(function() use($data, $service_id){

            $service = PublicCloudService::get()->byID($service_id);

            if(is_null($service))
                throw new NotFoundEntityException('PublicCloudService');

            $passport = $service->PublicCloudPassport();

            $is_passport = isset($data['is_passport']) ? $data['is_passport'] : 0;
            $learn_more = isset($data['learn_more']) ? Convert::raw2sql($data['learn_more']) : '';

            if ($is_passport) {
                if(is_null($passport))
                    $passport = new PublicCloudPassport();

                $passport->setLearnMoreLink($learn_more);
                $passport->PublicCloudID = $service_id;
                $passport->Active = 1;
                $passport->write();

            } else {
                if(!is_null($passport)) {
                    $passport->Active = 0;
                    $passport->write();
                }
            }

        });

    }

}