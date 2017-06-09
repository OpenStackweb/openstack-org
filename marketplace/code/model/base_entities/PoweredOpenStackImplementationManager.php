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
final class PoweredOpenStackImplementationManager implements IPoweredOpenStackImplementationManager
{
    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * @var IOpenStackPoweredServiceRepository
     */
    private $repository;

    /**
     * PoweredOpenStackImplementationManager constructor.
     * @param IOpenStackPoweredServiceRepository $repository
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
    public function updatePoweredProgram(array $data, $service_id){

        $this->tx_manager->transaction(function() use($data, $service_id){

            $service = OpenStackImplementation::get()->byID($service_id);

            if(is_null($service))
                throw new NotFoundEntityException();

            $snapshot = OpenStackImplementationPoweredSnapshotFactory::build($service, $data);

            if(isset($data['required_for_compute']))
                $service->CompatibleWithCompute = boolval($data['required_for_compute']);

            if(isset($data['required_for_storage']))
                $service->CompatibleWithStorage = boolval($data['required_for_storage']);

            if(isset($data['federated_identity']))
                $service->CompatibleWithFederatedIdentity = boolval($data['federated_identity']);

            if(isset($data['expiry_date']))
                $service->ExpiryDate = $data['expiry_date'];

            if(isset($data['program_version_id']))
                $service->ProgramVersionID = intval($data['program_version_id']);

            if(isset($data['reported_release_id']))
                $service->ReportedReleaseID = intval($data['reported_release_id']);

            if(isset($data['passed_release_id']))
                $service->PassedReleaseID = intval($data['passed_release_id']);

            if(isset($data['notes']))
                $service->Notes = trim($data['notes']);

            $service->write();
            $snapshot->write();

        });

    }

    /**
     * @param array $data
     * @param string $type
     * @param int $service_id
     * @return int
     * @throws NotFoundEntityException
     */
    function createImplementationLink(array $data, $type, $service_id)
    {
        $link                            = $type == 'zendesk' ? new ZenDeskLink() : new RefStackLink();
        $link->Link                      = trim($data['link']);
        $link->OpenStackImplementationID = $service_id;

        $link->write();
        return intval($link->ID);
    }

    /**
     * @param string $type
     * @param int $link_id
     * @param int $service_id
     * @return void
     * @throws NotFoundEntityException
     */
    function deleteImplementationLink($type, $link_id, $service_id)
    {
        $class       = $type == 'zendesk' ? 'ZenDeskLink' : 'RefStackLink';
        $link        = $class::get()->byID($link_id);
        if(is_null($link))
            throw new NotFoundEntityException();

        $link->delete();
    }

    /**
     * @param IMessageSenderService $service
     * @param int $days_about_expire_on
     * @return void
     */
    function sendExpiredPoweredProgramDigest(IMessageSenderService $service, $days_about_expire_on)
    {
        if(is_null($service)) return;

        $expired        = $this->repository->getAllExpired();
        $about_2_expire = $this->repository->getAllAboutToExpireOn($days_about_expire_on);

        $service->send([
            'expired'        => $expired,
            'about_2_expire' => $about_2_expire,
        ]);
    }
}