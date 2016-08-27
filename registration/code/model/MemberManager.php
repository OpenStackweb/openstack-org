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
final class MemberManager implements IMemberManager
{

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * @var IMemberRepository
     */
    private $repository;

    /**
     * @var IMemberFactory
     */
    private $factory;

    /**
     * @var ISecurityGroupRepository
     */
    private $group_repository;

    /**
     * @var ISecurityGroupFactory
     */
    private $group_factory;

    /**
     * @var IAffiliationFactory
     */
    private $affiliation_factory;

    /**
     * @var IOrgRepository
     */
    private $org_repository;

    /**
     * @var IOrgFactory
     */
    private $org_factory;

    /**
     * @var ISpeakerRegistrationRequestManager
     */
    private $speaker_registration_request_manager;

    public function __construct
    (
        IMemberRepository $repository,
        ISecurityGroupRepository $group_repository,
        IOrgRepository $org_repository,
        IMemberFactory $factory,
        ISecurityGroupFactory $group_factory,
        IAffiliationFactory $affiliation_factory,
        IOrgFactory $org_factory,
        ISpeakerRegistrationRequestManager $speaker_registration_request_manager,
        ITransactionManager $tx_manager
    )
    {
        $this->repository                           = $repository;
        $this->group_repository                     = $group_repository;
        $this->org_repository                       = $org_repository;
        $this->factory                              = $factory;
        $this->group_factory                        = $group_factory;
        $this->org_factory                          = $org_factory;
        $this->affiliation_factory                  = $affiliation_factory;
        $this->speaker_registration_request_manager = $speaker_registration_request_manager;
        $this->tx_manager                           = $tx_manager;
    }


    /**
     * @param array $data
     * @param IMessageSenderService $sender_service
     * @return null
     * @throws Exception
     */
    public function register(array $data, IMessageSenderService $sender_service)
    {
        $repository          = $this->repository;
        $group_repository    = $this->group_repository;
        $factory             = $this->factory;
        $group_factory       = $this->group_factory;
        $affiliation_factory = $this->affiliation_factory;
        $org_repository      = $this->org_repository;
        $org_factory         = $this->org_factory;

        try {
            return $this->tx_manager->transaction(function () use (
                $data,
                $repository,
                $group_repository,
                $org_repository,
                $factory,
                $group_factory,
                $affiliation_factory,
                $org_factory,
                $sender_service
            ) {
                $mandatory_fields = array
                (
                    'HiddenAffiliations' => 'Affiliations',
                    'Email'              => 'Email',
                    'FirstName'          => 'First Name',
                    'Surname'            => 'Surname',
                    'Password'           => 'Password',
                );

                foreach($mandatory_fields as $mf => $fn){
                    if (!isset($data[$mf]) || empty($data[$mf])) {
                        throw new EntityValidationException(sprintf('%s is a mandatory field!.',$fn));
                    }
                }

                if(!isset($data['Password']['_Password']) || !isset($data['Password']['_ConfirmPassword']) || $data['Password']['_ConfirmPassword'] !== $data['Password']['_Password'])
                {
                    throw new EntityValidationException('Password is a mandatory field!.');
                }

                $old_member = $repository->findByEmail(Convert::raw2sql($data['Email']));
                if (!is_null($old_member)) {
                    throw new EntityValidationException('Sorry, that email address already exists. Please choose another.');
                }

                $member = $factory->build($data);
                $member->write();

                $affiliations_data = json_decode($data["HiddenAffiliations"]);
                if(is_null($affiliations_data))
                    throw new EntityValidationException('You must at least enter one valid Affiliation.');
                if(!count((array)$affiliations_data))
                    throw new EntityValidationException('You must at least enter one valid Affiliation.');
                if ($data['MembershipType'] === 'foundation') {
                    $member->upgradeToFoundationMember();
                } else {
                    $member->convert2SiteUser();
                }

                $users_group = $group_repository->getByCode(ISecurityGroupFactory::UsersGroupCode);
                if (is_null($users_group)) {
                    // create group
                    $users_group = $group_factory->build(ISecurityGroupFactory::UsersGroupCode);
                    $users_group->write();
                }
                $member->addToGroupByCode(ISecurityGroupFactory::UsersGroupCode);
                foreach ($affiliations_data as $key => $d)
                {
                    $org_name = trim($d->OrgName);
                    $org = $org_repository->getByName($org_name);
                    if (is_null($org)) {
                        $org = $org_factory->build($org_name);
                        $org->write();
                    }
                    $affiliation = $affiliation_factory->build($d, $member, $org);
                    $affiliation->write();
                }
                if(!is_null($sender_service))
                    $sender_service->send($member);
                                //force write,
                $member->write();
                PublisherSubscriberManager::getInstance()->publish('new_user_registered', array($member->ID));
                return $member;
            });
        }
        catch(EntityValidationException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            throw $ex1;
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            throw $ex;
        }
    }

    /**
     * @param array $data
     * @param IMessageSenderService $sender_service
     * @return Member
     * @throws Exception
     */
    public function registerMobile(array $data, IMessageSenderService $sender_service)
    {
        $repository          = $this->repository;
        $group_repository    = $this->group_repository;
        $factory             = $this->factory;
        $group_factory       = $this->group_factory;
        $affiliation_factory = $this->affiliation_factory;
        $org_repository      = $this->org_repository;
        $org_factory         = $this->org_factory;

        try {
            return $this->tx_manager->transaction(function () use (
                $data,
                $repository,
                $group_repository,
                $org_repository,
                $factory,
                $group_factory,
                $affiliation_factory,
                $org_factory,
                $sender_service
            ) {
                $mandatory_fields = array
                (
                    'Email'              => 'Email',
                    'FirstName'          => 'First Name',
                    'Surname'            => 'Surname',
                    'Password'           => 'Password',
                );

                foreach($mandatory_fields as $mf => $fn){
                    if (!isset($data[$mf]) || empty($data[$mf])) {
                        throw new EntityValidationException(sprintf('%s is a mandatory field!.',$fn));
                    }
                }

                if(!isset($data['Password']['_Password']) || !isset($data['Password']['_ConfirmPassword']) || $data['Password']['_ConfirmPassword'] !== $data['Password']['_Password'])
                {
                    throw new EntityValidationException('Password is a mandatory field!.');
                }

                $old_member = $repository->findByEmail(Convert::raw2sql($data['Email']));
                if (!is_null($old_member)) {
                    throw new EntityValidationException('Sorry, that email address already exists. Please choose another.');
                }

                $member = $factory->buildReduced($data);
                $member->write();

                if($data['MembershipType'] !== 'community') {
                    throw new EntityValidationException('You can only register as a community member.');
                }

                $member->convert2SiteUser();
                // add to users group
                $users_group = $group_repository->getByCode(ISecurityGroupFactory::UsersGroupCode);
                if (is_null($users_group)) {
                    // create group
                    $users_group = $group_factory->build(ISecurityGroupFactory::UsersGroupCode);
                    $users_group->write();
                }
                $member->addToGroupByCode(ISecurityGroupFactory::UsersGroupCode);

                if (!is_null($sender_service)) {
                    $sender_service->send($member);
                }
                //force write,
                $member->write();
                PublisherSubscriberManager::getInstance()->publish('new_user_registered', array($member->ID));
                return $member;
            });
        }
        catch(EntityValidationException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            throw $ex1;
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            throw $ex;
        }
    }

    /**
     * @param string $token
     * @param IMessageSenderService $sender_service
     * @throws NotFoundEntityException
     * @return Member
     */
    public function verify($token, IMessageSenderService $sender_service)
    {
        $repository = $this->repository;
        return $this->tx_manager->transaction(function () use ($token, $repository, $sender_service){
            $member = $repository->getByEmailVerificationToken($token);
            if(is_null($member)) throw new NotFoundEntityException('Member', sprintf('email verification token %s', $token));
            $member->doEmailConfirmation($token);
            $sender_service->send($member);
            return $member;
        });
    }

    /**
     * @param $email
     * @param IMessageSenderService $sender_service
     * @throws NotFoundEntityException
     * @throws EntityValidationException
     * @return Member
     */
    public function resendEmailVerification($email, IMessageSenderService $sender_service)
    {
        $repository = $this->repository;

        return $this->tx_manager->transaction(function () use ($email, $repository, $sender_service){

            if(filter_var($email, FILTER_VALIDATE_EMAIL) === false)
                throw new EntityValidationException('invalid mail address');

            $member = $repository->findByEmail($email);
            if (is_null($member))
                throw new NotFoundEntityException('Member', $email);

            if($member->EmailVerified) throw new EntityValidationException('Member already verified!');
            $sender_service = new MemberRegistrationSenderService();
            $sender_service->send($member);
        });
    }

    /**
     * @param Member $member
     * @param IMessageSenderService $sender_service
     * @return Member
     */
    public function resetEmailVerification(Member $member, IMessageSenderService $sender_service){
        return $this->tx_manager->transaction(function () use ($member, $sender_service){
            $member->resetConfirmation();
            $member->write();
            $this->resendEmailVerification($member->Email, $sender_service);
            return $member;
        });
    }

    /**
     * Register an speaker and confirm the registration request if exists
     * @param array $data
     * @param IMessageSenderService $sender_service
     * @return Member
     * @throws EntityValidationException
     * @throws Exception
     */
    public function registerSpeaker(array $data, IMessageSenderService $sender_service)
    {
        $repository                           = $this->repository;
        $group_repository                     = $this->group_repository;
        $factory                              = $this->factory;
        $group_factory                        = $this->group_factory;
        $speaker_registration_request_manager = $this->speaker_registration_request_manager;

        try {
            return $this->tx_manager->transaction(function () use (
                $data,
                $repository,
                $group_repository,
                $factory,
                $group_factory,
                $speaker_registration_request_manager,
                $sender_service
            ) {
                $mandatory_fields = array
                (
                    'Email'              => 'Email',
                    'FirstName'          => 'First Name',
                    'Surname'            => 'Surname',
                    'Password'           => 'Password',
                );

                foreach($mandatory_fields as $mf => $fn){
                    if (!isset($data[$mf]) || empty($data[$mf])) {
                        throw new EntityValidationException(sprintf('%s is a mandatory field!.',$fn));
                    }
                }

                if(!isset($data['Password']['_Password']) || !isset($data['Password']['_ConfirmPassword']) || $data['Password']['_ConfirmPassword'] !== $data['Password']['_Password'])
                {
                    throw new EntityValidationException('Password is a mandatory field!.');
                }

                $old_member = $repository->findByEmail(Convert::raw2sql($data['Email']));
                if (!is_null($old_member)) {
                    throw new EntityValidationException('Sorry, that email address already exists. Please choose another.');
                }

                $member = $factory->buildReduced($data);
                $member->write();
                $member->convert2SiteUser();

                $speakers_group = $group_repository->getByCode(ISecurityGroupFactory::SpeakersGroupCode);
                if (is_null($speakers_group)) {
                    // create group
                    $speakers_group = $group_factory->build(ISecurityGroupFactory::SpeakersGroupCode);
                    $speakers_group->write();
                }

                $member->addToGroupByCode(ISecurityGroupFactory::SpeakersGroupCode);

                if (!empty($data[SpeakerRegistrationRequest::ConfirmationTokenParamName])) {

                    $speaker_registration_token = $data[SpeakerRegistrationRequest::ConfirmationTokenParamName];
                    $speaker_registration_request_manager->confirm($speaker_registration_token, $member);
                }

                if (!is_null($sender_service)) {
                    $sender_service->send($member);
                }
                //force write,
                $member->write();
                PublisherSubscriberManager::getInstance()->publish('new_user_registered', array($member->ID));
                return $member;
            });
        }
        catch(EntityValidationException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            throw $ex1;
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            throw $ex;
        }
    }
}