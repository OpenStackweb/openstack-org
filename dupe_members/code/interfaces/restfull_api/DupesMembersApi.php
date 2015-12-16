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
 * Class DupesMembersApi
 */
final class DupesMembersApi
    extends AbstractRestfulJsonApi {

    const ApiPrefix = 'api/v1/dupes-members';

    /**
     * @var DupesMembersManager
     */
    private $manager;

    public function __construct(){
        parent::__construct();
        $this->manager = new DupesMembersManager(new SapphireDupesMemberRepository,
            new DupeMemberMergeRequestFactory,
            new DupeMemberDeleteRequestFactory,
            new SapphireDupeMemberMergeRequestRepository,
            new SapphireDupeMemberDeleteRequestRepository,
            new SapphireDeletedDupeMemberRepository,
            new DeletedDupeMemberFactory,
            new SapphireCandidateNominationRepository,
            new SapphireNotMyAccountActionRepository,
            new NotMyAccountActionFactory,
            SapphireTransactionManager::getInstance(),
            SapphireBulkQueryRegistry::getInstance());
    }

    protected function isApiCall(){
        $request = $this->getRequest();
        if(is_null($request)) return false;
        return  strpos(strtolower($request->getURL()),self::ApiPrefix) !== false;
    }

    protected function authorize()
    {
        if(!$this->checkOwnAjaxRequest()) return false;
        if(Member::currentUser() === null) return false;
        return true;
    }

    /**
     * @var array
     */
    static $url_handlers = array(
        'POST $CONFIRMATION_TOKEN/merge'           => 'mergeAccount',
        'POST $MEMBER_ID/merge-request'            => 'mergeAccountRequest',
        'POST $MEMBER_ID/delete-request'           => 'deleteAccountRequest',
        'DELETE $CONFIRMATION_TOKEN/account'       => 'deleteAccount',
        'PUT $CONFIRMATION_TOKEN/account'          => 'keepAccount',
        'PATCH $CONFIRMATION_TOKEN/account'        => 'upgradeDeleteRequest2Merge',
        'PATCH show/profile/$SHOW'                 => 'showDupesOnProfile',
        'PATCH foreign-account/$FOREIGN_MEMBER_ID' => 'markForeignAccount',
    );

    /**
     * @var array
     */
    static $allowed_actions = array(
        'mergeAccountRequest',
        'deleteAccountRequest',
        'deleteAccount',
        'keepAccount',
        'upgradeDeleteRequest2Merge',
        'mergeAccount',
        'showDupesOnProfile',
        'markForeignAccount',
    );

    public function mergeAccountRequest()
    {
        $member_id = (int)convert::raw2sql($this->request->param('MEMBER_ID'));
        try{
            $this->manager->registerMergeAccountRequest(Member::currentUser(), $member_id, new DupeMemberActionRequestEmailNotificationSender(new SapphireDupeMemberMergeRequestRepository,
                new SapphireDupeMemberDeleteRequestRepository));
            return $this->ok();
        }
        catch(NotFoundEntityException $ex1){
            SS_Log::log($ex1,SS_Log::WARN);
            return $this->notFound($ex1->getMessage());
        }
        catch(EntityValidationException $ex2){
            SS_Log::log($ex2,SS_Log::WARN);
            return $this->validationError($ex2->getMessages());
        }
        catch(Exception $ex){
            SS_Log::log($ex,SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function deleteAccountRequest()
    {
        $member_id = (int)convert::raw2sql($this->request->param('MEMBER_ID'));
        try{
            $this->manager->registerDeleteAccountRequest(Member::currentUser(), $member_id,
                new DupeMemberActionRequestEmailNotificationSender(new SapphireDupeMemberMergeRequestRepository, new SapphireDupeMemberDeleteRequestRepository));
            return $this->ok();
        }
        catch(NotFoundEntityException $ex1){
            SS_Log::log($ex1,SS_Log::WARN);
            return $this->notFound($ex1->getMessage());
        }
        catch(EntityValidationException $ex2){
            SS_Log::log($ex2,SS_Log::WARN);
            return $this->validationError($ex2->getMessages());
        }
        catch(Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function deleteAccount(){
        $token = convert::raw2sql($this->request->param('CONFIRMATION_TOKEN'));
        try{
            $current_member = Member::currentUser();
            $this->manager->deleteAccount($current_member, $token);
            return $this->ok();
        }
        catch(NotFoundEntityException $ex1){
            SS_Log::log($ex1,SS_Log::WARN);
            return $this->notFound($ex1->getMessage());
        }
        catch(EntityValidationException $ex2){
            SS_Log::log($ex2,SS_Log::WARN);
            return $this->validationError($ex2->getMessages());
        }
        catch(Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function keepAccount(){
        $token = convert::raw2sql($this->request->param('CONFIRMATION_TOKEN'));
        try{
            $current_member = Member::currentUser();
            $this->manager->keepAccount($current_member, $token);
            return $this->ok();
        }
        catch(NotFoundEntityException $ex1){
            SS_Log::log($ex1,SS_Log::WARN);
            return $this->notFound($ex1->getMessage());
        }
        catch(EntityValidationException $ex2){
            SS_Log::log($ex2,SS_Log::WARN);
            return $this->validationError($ex2->getMessages());
        }
        catch(Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function upgradeDeleteRequest2Merge(){
        $token = convert::raw2sql($this->request->param('CONFIRMATION_TOKEN'));
        try{
            $current_member = Member::currentUser();
            $this->manager->upgradeDeleteRequest2Merge($current_member, $token, new DupeMemberActionRequestEmailNotificationSender(new SapphireDupeMemberMergeRequestRepository, new SapphireDupeMemberDeleteRequestRepository));
            return $this->ok();
        }
        catch(NotFoundEntityException $ex1){
            SS_Log::log($ex1,SS_Log::WARN);
            return $this->notFound($ex1->getMessage());
        }
        catch(EntityValidationException $ex2){
            SS_Log::log($ex2,SS_Log::WARN);
            return $this->validationError($ex2->getMessages());
        }
        catch(Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function mergeAccount(){
        try{
            $token = convert::raw2sql($this->request->param('CONFIRMATION_TOKEN'));
            $data = $this->getJsonRequest();
            if (!$data) return $this->serverError();
            $current_member = Member::currentUser();
            $this->manager->mergeAccount($current_member, $token, $data, new MergeAccountBulkQueryFactory);
            return $this->ok();
        }
        catch(NotFoundEntityException $ex1){
            SS_Log::log($ex1,SS_Log::WARN);
            return $this->notFound($ex1->getMessage());
        }
        catch(EntityValidationException $ex2){
            SS_Log::log($ex2,SS_Log::WARN);
            return $this->validationError($ex2->getMessages());
        }
        catch(Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function showDupesOnProfile(){
        try{
            $show =  convert::raw2sql($this->request->param('SHOW'));
            $show = filter_var($show, FILTER_VALIDATE_BOOLEAN);
            $current_member = Member::currentUser();
            $this->manager->showDupesOnProfile($current_member->ID, $show);
            return $this->ok();
        }
        catch(NotFoundEntityException $ex1){
            SS_Log::log($ex1,SS_Log::WARN);
            return $this->notFound($ex1->getMessage());
        }
        catch(EntityValidationException $ex2){
            SS_Log::log($ex2,SS_Log::WARN);
            return $this->validationError($ex2->getMessages());
        }
        catch(Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function markForeignAccount(){
        try{
            $foreign_id     = intval(convert::raw2sql($this->request->param('FOREIGN_MEMBER_ID')));
            $current_member = Member::currentUser();
            $this->manager->markAsNotMyAccount($current_member->ID, $foreign_id);
            return $this->ok();
        }
        catch(NotFoundEntityException $ex1){
            SS_Log::log($ex1,SS_Log::WARN);
            return $this->notFound($ex1->getMessage());
        }
        catch(EntityValidationException $ex2){
            SS_Log::log($ex2,SS_Log::WARN);
            return $this->validationError($ex2->getMessages());
        }
        catch(Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }
} 