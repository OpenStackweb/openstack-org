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

final class MemberActivationController extends AbstractController
{

    /**
     * @var IMemberManager
     */
    private $member_manager;

    /**
     * @return IMemberManager
     */
    public function getMemberManager()
    {
        return $this->member_manager;
    }

    /**
     * @param IMemberManager $manager
     */
    public function setMemberManager(IMemberManager $manager)
    {
        $this->member_manager = $manager;
    }

    static $allowed_actions = [
        'ActivateMember',
        'DeactivateMember',
    ];

    static $url_handlers = [
        'GET $MEMBERID/activate'   => 'ActivateMember',
        'GET $MEMBERID/deactivate' => 'DeactivateMember',
    ];

    public function init()
    {
        parent::init();
        Page_Controller::AddRequirements();
    }

    public function ActivateMember(SS_HTTPRequest $request)
    {
        try {
            $current_user = Member::currentUser();
            if(!$current_user){
                return OpenStackIdCommon::doLogin($request->getURL(true));
            }

            if(!$current_user->isAdmin()){
                return $this->httpError(413, "Not Authorized");
            }

            $member_id = intval(Convert::raw2sql($this->request->param('MEMBERID')));
            $member = Member::get()->byID($member_id);
            if(!$member)
                throw new NotFoundEntityException();
            $this->member_manager->activate($member);

            return $this->renderWith
            (
                array
                (
                    'MemberActivation_activated',
                    'Page'
                ),
                array
                (
                    'Member' => $member,
                )
            );
        }
        catch (NotFoundEntityException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->httpError(404, "Member not found");
        }
        catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->renderWith(array('MemberActivation_Error', 'Page'));
        }
    }

    public function DeActivateMember(SS_HTTPRequest $request)
    {
        try {
            $current_user = Member::currentUser();
            if(!$current_user){
                return OpenStackIdCommon::doLogin($request->getURL(true));
            }

            if(!$current_user->isAdmin()){
                return $this->httpError(413, "Not Authorized");
            }

            $member_id = intval(Convert::raw2sql($this->request->param('MEMBERID')));
            $member = Member::get()->byID($member_id);
            if(!$member)
                throw new NotFoundEntityException();
            $this->member_manager->deactivate($member);

            return $this->renderWith
            (
                array
                (
                    'MemberActivation_deactivated',
                    'Page'
                ),
                array
                (
                    'Member' => $member,
                )
            );
        }
        catch (NotFoundEntityException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->httpError(404, "Member not found");
        }
        catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->renderWith(array('MemberActivation_Error', 'Page'));
        }
    }

}