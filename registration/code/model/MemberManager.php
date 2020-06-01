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
 * Class MemberManager
 */
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

    /**
     * MemberManager constructor.
     * @param IMemberRepository $repository
     * @param ISecurityGroupRepository $group_repository
     * @param IOrgRepository $org_repository
     * @param IMemberFactory $factory
     * @param ISecurityGroupFactory $group_factory
     * @param IAffiliationFactory $affiliation_factory
     * @param IOrgFactory $org_factory
     * @param ISpeakerRegistrationRequestManager $speaker_registration_request_manager
     * @param ITransactionManager $tx_manager
     */
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
     * @param Member $member
     * @param array $data
     * @return Member
     * @throws EntityValidationException
     */
    public function register(Member $member, array $data):Member
    {

        try {
            return $this->tx_manager->transaction(function () use (
                $member,
                $data
            ) {
                $mandatory_fields = [
                    'HiddenAffiliations' => 'Affiliations',
                ];

                foreach($mandatory_fields as $mf => $fn){
                    if (!isset($data[$mf]) || empty($data[$mf])) {
                        throw new EntityValidationException(sprintf('%s is a mandatory field!.',$fn));
                    }
                }

                $member = $this->factory->populate($member, $data);
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

                $users_group = $this->group_repository->getByCode(ISecurityGroupFactory::UsersGroupCode);
                if (is_null($users_group)) {
                    // create group
                    $users_group = $this->group_factory->build(ISecurityGroupFactory::UsersGroupCode);
                    $users_group->write();
                }
                $member->addToGroupByCode(ISecurityGroupFactory::UsersGroupCode);
                foreach ($affiliations_data as $key => $d)
                {
                    $org_name = trim($d->OrgName);
                    $org = $this->org_repository->getByName($org_name);
                    if (is_null($org)) {
                        $org = $this->org_factory->build($org_name);
                        $org->write();
                    }
                    $affiliation = $this->affiliation_factory->build($d, $member, $org);
                    $affiliation->write();
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
     * @param Member $member
     * @return void
     */
    public function activate(Member $member)
    {
        return $this->tx_manager->transaction(function () use ($member){
            $member->activate();
            $member->write();
        });
    }

    /**
     * @param Member $member
     * @return void
     */
    public function deactivate(Member $member)
    {
        return $this->tx_manager->transaction(function () use ($member){
            $member->deActivate();
            $member->write();
        });
    }

    /**
     * @param mixed $claims
     * @return Member
     */
    public function registerByClaims($claims): Member
    {
       return $this->tx_manager->transaction(function() use($claims){
           $externalId     = $claims->sub;
           $email          = $claims->email;

           // check if member exists using external id

           $member = $this->repository->findByExternalId($externalId);
           if(is_null($member)){
               // check by primary email
               $member = $this->repository->findByPrimaryEmail($email);
           }

           if(is_null($member)){
               // we should create a new user from scratch
               $member = new Member();
               $member->ExternalUserId = $externalId;
               $member->Email          = $email;
               $member->FirstName      = $claims->given_name;
               $member->Surname        = $claims->family_name;
               $member->write();
           }
           else{
               // member already exists , calculate membership type
               $membershipType =  IOpenStackMember::MembershipTypeNone;
               if($member->isFoundationMember()){
                   $membershipType =  IOpenStackMember::MembershipTypeFoundation;
               }
               else if($member->isCommunityMember()){
                   $membershipType =  IOpenStackMember::MembershipTypeCommunity;
               }
               $member->MembershipType = $membershipType;
               $member->write();
           }

           if($member->canLogIn())
           {
               $member->FirstName            = $claims->given_name;
               $member->Surname              = $claims->family_name;
               $member->Email                = $email;
               $member->Locale               = $claims->locale;
               $member->Bio                  = $claims->bio;
               $member->Gender               = $claims->gender;
               $member->StatementOfInterest  = $claims->statement_of_interest;
               $member->Address              = $claims->address->street_address;
               $member->Country              = $claims->address->country;
               $member->State                = $claims->address->region;
               $member->City                 = $claims->address->locality;
               $member->Country              = $claims->address->country;
               // social handlers
               $member->GitHubUser           = $claims->github_user;
               $member->IRCHandle            = $claims->irc;
               $member->TwitterName          = $claims->twitter_name;
               $member->WeChatUser           = $claims->wechat_user;
               $member->LinkedInProfile      = $claims->linked_in_profile;
               $member->ExternalUserId       = $externalId;
               $member->write();
               $member->LogIn(true);
               // check idps user groups
               $idp_groups = $claims->groups;
               foreach($idp_groups as $idp_group){
                   $slug = $idp_group->slug == ISecurityGroupFactory::SuperAdmins ? ISecurityGroupFactory::Administrators : $idp_group->slug;
                   if(!$member->inGroup($slug)){
                       $member->addToGroupByCode($slug);
                   }
               }
               // check if we have in session a pending SpeakerRegistrationRequest
               $speaker_registration_token = Session::get(SpeakerRegistrationRequest::ConfirmationTokenParamName);
               if(!empty($speaker_registration_token)) {
                   $speakers_group = $this->group_repository->getByCode(ISecurityGroupFactory::SpeakersGroupCode);
                   if (is_null($speakers_group)) {
                       // create group
                       $speakers_group = $this->group_factory->build(ISecurityGroupFactory::SpeakersGroupCode);
                       $speakers_group->write();
                   }
                   $member->addToGroupByCode(ISecurityGroupFactory::SpeakersGroupCode);
                   $this->speaker_registration_request_manager->confirm($speaker_registration_token, $member);
                   Session::clear(SpeakerRegistrationRequest::ConfirmationTokenParamName);
               }

               return $member;
           }
           throw new Exception("Inactive User!");
       });
    }
}