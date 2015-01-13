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
 * Class DupesMembersManager
 */
final class DupesMembersManager {

    /**
     * @var IMemberRepository
     */
    private $member_repository;

    /**
     * @var IDupeMemberActionAccountRequestFactory
     */
    private $merge_request_factory;

    /**
     * @var IDupeMemberActionAccountRequestFactory
     */
    private $delete_request_factory;

    /**
     * @var IDupeMemberActionAccountRequestRepository
     */
    private $merge_request_repository;

    /**
     * @var IDupeMemberActionAccountRequestRepository
     */
    private $delete_request_repository;

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * @param IMemberRepository                         $member_repository
     * @param IDupeMemberActionAccountRequestFactory    $merge_request_factory
     * @param IDupeMemberActionAccountRequestFactory    $delete_request_factory
     * @param IDupeMemberActionAccountRequestRepository $merge_request_repository
     * @param IDupeMemberActionAccountRequestRepository $delete_request_repository
     * @param ITransactionManager                       $tx_manager
     */
    public function __construct(IMemberRepository $member_repository,
                                IDupeMemberActionAccountRequestFactory $merge_request_factory,
                                IDupeMemberActionAccountRequestFactory $delete_request_factory,
                                IDupeMemberActionAccountRequestRepository $merge_request_repository,
                                IDupeMemberActionAccountRequestRepository $delete_request_repository,
                                IEntityRepository $delete_dupe_member_repository,
                                IDeletedDupeMemberFactory $delete_dupe_member_factory,
                                ITransactionManager $tx_manager ) {

        $this->member_repository             = $member_repository;
        $this->merge_request_factory         = $merge_request_factory;
        $this->delete_request_factory        = $delete_request_factory;
        $this->merge_request_repository      = $merge_request_repository;
        $this->delete_request_repository     = $delete_request_repository;
        $this->delete_dupe_member_repository = $delete_dupe_member_repository;
        $this->delete_dupe_member_factory    = $delete_dupe_member_factory;
        $this->tx_manager                    = $tx_manager;
    }

    public function HasDupes(ICommunityMember $member) {
        $res = $this->member_repository->getAllByName($member->getFirstName(), $member->getLastName());
        for($i = 0 ; $i < count($res);$i++){
            if($res[$i]->getIdentifier()==$member->getIdentifier()){
                unset($res[$i]);
                break;
            }
        }
        return count($res) > 0;
    }

    public function getDupes(ICommunityMember $member){
        list($res,$count) = $this->member_repository->getAllByName($member->getFirstName(), $member->getLastName());
        for($i = 0 ; $i < count($res);$i++){
            if($res[$i]->getIdentifier()==$member->getIdentifier()){
                unset($res[$i]);
                break;
            }
        }
        return $res;
    }

    public function registerMergeAccountRequest(ICommunityMember $member, $account_id, IDupeMemberActionRequestNotificationSender $notification_sender) {

        $member_repository        = $this->member_repository ;
        $merge_request_factory    = $this->merge_request_factory;
        $merge_request_repository = $this->merge_request_repository;

        return $this->tx_manager->transaction(function() use($member, $account_id, $member_repository, $merge_request_factory, $merge_request_repository, $notification_sender){

            $dupe_account = $member_repository->getById($account_id);
            if(is_null($dupe_account)) throw new NotFoundEntityException('Member' , sprintf("member id %s", $account_id));

            $old_request = $merge_request_repository->findByDupeAccount($dupe_account->getEmail());

            if(!is_null($old_request))
                throw new EntityValidationException(array(
                array('message' => sprintf('merge request already exists for %s account ',$dupe_account->getEmail()))
            ));

            $request = $merge_request_factory->build($member, $dupe_account);

            $merge_request_repository->add($request);

            $notification_sender->sendMergeNotification($request);

            return $request;
        });
    }

    public function registerDeleteAccountRequest(ICommunityMember $member, $account_id, IDupeMemberActionRequestNotificationSender $notification_sender) {

        $member_repository         = $this->member_repository ;
        $delete_request_factory    = $this->delete_request_factory;
        $delete_request_repository = $this->delete_request_repository;

        return $this->tx_manager->transaction(function() use($member, $account_id, $member_repository, $delete_request_factory, $delete_request_repository, $notification_sender){

            $dupe_account = $member_repository->getById($account_id);
            if(is_null($dupe_account)) throw new NotFoundEntityException('Member' , sprintf("member id %s", $account_id));

            $old_request = $delete_request_repository->findByDupeAccount($dupe_account->getEmail());

            if(!is_null($old_request))
                throw new EntityValidationException(array(
                    array('message' => sprintf('delete request already exists for %s account ',$dupe_account->getEmail()))
                ));

            $request = $delete_request_factory->build($member, $dupe_account);

            $delete_request_repository->add($request);

            $notification_sender->sendDeleteNotification($request);

            return $request;
        });
    }

    public function deleteAccount(ICommunityMember $current_member, $token){

        $delete_request_repository     = $this->delete_request_repository;
        $member_repository             = $this->member_repository ;
        $delete_dupe_member_factory    = $this->delete_dupe_member_factory;
        $delete_dupe_member_repository = $this->delete_dupe_member_repository;

        return $this->tx_manager->transaction(function() use($current_member, $token,  $delete_request_repository, $member_repository, $delete_dupe_member_factory, $delete_dupe_member_repository){

            $request = $delete_request_repository->findByConfirmationToken($token);

            if(is_null($request)) throw new NotFoundEntityException('DupeMemberDeleteRequest' , sprintf("token %s", $token));

            if($request->isVoid())
                throw new DuperMemberActionRequestVoid();

            $dupe_account =  $request->getDupeAccount();

            if($dupe_account->getEmail() != $current_member->getEmail()){
                throw new AccountActionBelongsToAnotherMemberException;
            }

            $request->doConfirmation($token);

            $current_member->resign();
            $current_member->logOut();
            $deleted  = $delete_dupe_member_factory->build($current_member);

            $member_repository->delete($current_member);

            $delete_dupe_member_repository->add($deleted);

            return $request;
        });

    }

    public function keepAccount(ICommunityMember $current_member, $token){

        $delete_request_repository = $this->delete_request_repository;
        $member_repository         = $this->member_repository ;

        return $this->tx_manager->transaction(function() use($current_member, $token,  $delete_request_repository, $member_repository){

            $request = $delete_request_repository->findByConfirmationToken($token);

            if(is_null($request)) throw new NotFoundEntityException('DupeMemberDeleteRequest' , sprintf("token %s", $token));

            if($request->isVoid())
                throw new DuperMemberActionRequestVoid();

            $dupe_account =  $request->getDupeAccount();

            if($dupe_account->getEmail() != $current_member->getEmail()){
                throw new AccountActionBelongsToAnotherMemberException;
            }

            $delete_request_repository->delete($request);

            return $request;
        });

    }

    public function upgradeDeleteRequest2Merge(ICommunityMember $current_member, $token, IDupeMemberActionRequestNotificationSender $notification_sender) {
        $_this = $this;

        $this->tx_manager->transaction(function() use($_this, $current_member, $token, $notification_sender){
            $request = $_this->keepAccount($current_member, $token);

            $_this->registerMergeAccountRequest($request->getPrimaryAccount() , $request->getDupeAccount()->getIdentifier(), $notification_sender);
        });
    }
} 