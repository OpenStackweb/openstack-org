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
 * Class DupesMembers_Controller
 */
final class DupesMembers_Controller extends AbstractController {

    static $url_handlers = array(
        'GET $CONFIRMATION_TOKEN/merge'  => 'mergeAccount',
        'GET $CONFIRMATION_TOKEN/delete' => 'deleteAccount',
    );

    /**
     * @var array
     */
    static $allowed_actions = array(
        'mergeAccount',
        'deleteAccount'
    );

    /**
     * @var SapphireDupeMemberDeleteRequestRepository
     */
    private $delete_request_repository;

    /**
     * @var SapphireDupeMemberDeleteRequestRepository
     */
    private $merge_request_repository;


    /**
     * @var DupesMembersManager 
     */
    private $manager;

    public function __construct()
    {
        parent::__construct();

        $this->delete_request_repository = new SapphireDupeMemberDeleteRequestRepository;
        $this->merge_request_repository  = new SapphireDupeMemberMergeRequestRepository;

        $this->manager = new DupesMembersManager(new SapphireDupesMemberRepository,
            new DupeMemberMergeRequestFactory,
            new DupeMemberDeleteRequestFactory,
            $this->merge_request_repository,
            $this->delete_request_repository,
            new SapphireDeletedDupeMemberRepository,
            new DeletedDupeMemberFactory,
            new SapphireCandidateNominationRepository,
            new SapphireNotMyAccountActionRepository,
            new NotMyAccountActionFactory,
            SapphireTransactionManager::getInstance(),
            SapphireBulkQueryRegistry::getInstance());
    }


    public function init()
    {
        parent::init();
        Page_Controller::AddRequirements();
        Requirements::javascript("marketplace/code/ui/admin/js/utils.js");
    }
    /**
     * @return string|void
     */
    public function mergeAccount() {

        $token = $this->request->param('CONFIRMATION_TOKEN');
        try{
            $current_member = Member::currentUser();
            if(is_null($current_member))
                return Controller::curr()->redirect("/Security/login?BackURL=" . urlencode($_SERVER['REQUEST_URI']));
            $request = $this->merge_request_repository->findByConfirmationToken($token);

            if(is_null($request) ||  $request->isVoid())
                throw new DuperMemberActionRequestVoid();

            $dupe_account =  $request->getDupeAccount();

            if($dupe_account->getEmail() != $current_member->getEmail()){
                throw new AccountActionBelongsToAnotherMemberException;
            }

            $any_account_has_gerrit = $request->getDupeAccount()->isGerritUser() || $request->getPrimaryAccount()->isGerritUser();
            $any_account_has_gerrit = $any_account_has_gerrit?'true':'false';

            Requirements::customScript('var any_account_has_gerrit = '.$any_account_has_gerrit.';');
            Requirements::javascript('dupe_members/javascript/dupe.members.merge.action.js');

            return $this->renderWith(array('DupesMembers_MergeAccountConfirm', 'Page'), array(
                'DupeAccount'       => $request->getDupeAccount(),
                'CurrentAccount'    => $request->getPrimaryAccount(),
                'ConfirmationToken' => $token,
            ));
        }
        catch(AccountActionBelongsToAnotherMemberException $ex1){
            SS_Log::log($ex1,SS_Log::ERR);
            $request = $this->merge_request_repository->findByConfirmationToken($token);
            return $this->renderWith(array('DupesMembers_belongs_2_another_user','Page'), array(
                'UserName' => $request->getDupeAccount()->getEmail()
            ));
        }
        catch(Exception $ex){
            SS_Log::log($ex,SS_Log::ERR);
            return $this->renderWith(array('DupesMembers_error','Page'));
        }
    }

    public function currentRequestAnyAccountHasGerrit(){
        $token = $this->request->param('CONFIRMATION_TOKEN');
        if(empty($token)) return false;
        $request = $this->merge_request_repository->findByConfirmationToken($token);
        if(is_null($request) ||  $request->isVoid())
            return false;
        $any_account_has_gerrit = $request->getDupeAccount()->isGerritUser() || $request->getPrimaryAccount()->isGerritUser();
        return $any_account_has_gerrit;
    }

    /**
     * @return string|void
     */
    public function deleteAccount() {

        $token = $this->request->param('CONFIRMATION_TOKEN');

        try{
            $current_member = Member::currentUser();
            if(is_null($current_member))
                return Controller::curr()->redirect("Security/login?BackURL=" . urlencode($_SERVER['REQUEST_URI']));
            $request = $this->delete_request_repository->findByConfirmationToken($token);

            if(is_null($request) || $request->isVoid())
                throw new DuperMemberActionRequestVoid();

            $dupe_account =  $request->getDupeAccount();

            if($dupe_account->getEmail() != $current_member->getEmail()){
                throw new AccountActionBelongsToAnotherMemberException;
            }

            Requirements::javascript('dupe_members/javascript/dupe.members.delete.action.js');

            return $this->renderWith(array('DupesMembers_DeleteAccountConfirm', 'Page'), array(
                    'DupeAccount'       => $request->getDupeAccount(),
                    'CurrentAccount'    => $request->getPrimaryAccount(),
                    'ConfirmationToken' => $token,
            ));

        }
        catch(AccountActionBelongsToAnotherMemberException $ex1){
            SS_Log::log($ex1,SS_Log::ERR);
            $request = $this->delete_request_repository->findByConfirmationToken($token);
            return $this->renderWith(array('DupesMembers_belongs_2_another_user','Page'), array(
                'UserName' => $request->getDupeAccount()->getEmail()
            ));
        }
        catch(Exception $ex){
            SS_Log::log($ex,SS_Log::ERR);
            return $this->renderWith(array('DupesMembers_error','Page'));
        }
    }
} 