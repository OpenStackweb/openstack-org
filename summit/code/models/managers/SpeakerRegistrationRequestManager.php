<?php
/**
 * Copyright 2015 OpenStack Foundation
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
 * Class SpeakerRegistrationRequestManager
 */
final class SpeakerRegistrationRequestManager
    implements ISpeakerRegistrationRequestManager {

    /**
     * @var ISpeakerRegistrationRequestRepository
     */
    private $repository;

    /**
     * @var ISpeakerRegistrationRequestFactory
     */
    private $factory;

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * @param ISpeakerRegistrationRequestRepository $repository
     * @param ISpeakerRegistrationRequestFactory $factory
     * @param ITransactionManager $tx_manager
     */
    public function __construct(ISpeakerRegistrationRequestRepository $repository,
                                ISpeakerRegistrationRequestFactory $factory,
                                ITransactionManager $tx_manager){

        $this->repository = $repository;
        $this->factory    = $factory;
        $this->tx_manager = $tx_manager;
    }

    /**
     * @param IPresentationSpeaker $speaker
     * @param string $email
     * @return ISpeakerRegistrationRequest
     * @throws EntityAlreadyExistsException
     */
    public function register(IPresentationSpeaker $speaker, $email)
    {
        $repository = $this->repository;
        $factory    = $this->factory;
        return $this->tx_manager->transaction(function () use($speaker, $email, $repository, $factory){

            $old_request = $repository->getByEmail($email);
            if(!is_null($old_request))
                throw new EntityAlreadyExistsException('SpeakerRegistrationRequest', sprintf("email %s", $email));

            $request = $factory->build($speaker, $email);

            $request->write();

            return $request;
        });
    }

    /**
     * @param string $token
     * @param ICommunityMember $member
     * @return void
     */
    public function confirm($token, ICommunityMember $member)
    {
        $repository = $this->repository;

        return $this->tx_manager->transaction(function () use($token, $member, $repository){

            $old_request = $repository->getByConfirmationToken($token);
            if(is_null($old_request)) throw new NotFoundEntityException('ISpeakerRegistrationRequest', sprintf("token %s", $token));

            $old_request->confirm($token);

            $old_request->associatedSpeaker()->associateMember($member);

        });
    }
}