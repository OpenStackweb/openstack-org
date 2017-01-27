<?php

/**
 * Copyright 2016 OpenStack Foundation
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
class MemberProfileAPI extends AbstractRestfulJsonApi
{
    public function __construct() {
        parent::__construct();
    }

    protected function isApiCall(){
        $request = $this->getRequest();
        if(is_null($request)) return false;
        return true;
    }

    /**
     * @return bool
     */
    protected function authorize(){
        return Member::currentUser();
    }

    protected function authenticate() {
        return true;
    }

    static $url_handlers = array(
        'GET dismiss-update-profile'  => 'dismissUpdateProfileModal',
    );

    static $allowed_actions = array(
        'dismissUpdateProfileModal',

    );


    public function dismissUpdateProfileModal(SS_HTTPRequest $request)
    {
        if (Director::is_ajax() && Member::currentUser()) {
            Session::set("Member.showUpdateProfileModal", false);
            return $this->ok();
        }
    }
}