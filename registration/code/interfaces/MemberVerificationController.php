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
final class MemberVerificationController extends AbstractController
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

    static $allowed_actions = array(
        'VerifyMemberEmail',
    );

    static $url_handlers = array(
        'GET $TOKEN!' => 'VerifyMemberEmail',
    );

    public function init()
    {
        parent::init();
        Page_Controller::AddRequirements();
    }

    public function VerifyMemberEmail(SS_HTTPRequest $request)
    {
        try {
            $token = Convert::raw2sql($this->request->param('TOKEN'));

            if(is_null($token))
                throw new EntityValidationException('missing token!');

            $member = $this->member_manager->verify($token, new MemberRegistrationVerifiedSenderService);

            return $this->renderWith
            (
                array
                (
                    'MemberVerification_verified',
                    'Page'
                ),
                array
                (
                    'Member' => $member,
                )
            );
        } catch (NotFoundEntityException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->renderWith(array('MemberVerification_error', 'Page'));
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->renderWith(array('MemberVerification_error', 'Page'));
        }
    }
}