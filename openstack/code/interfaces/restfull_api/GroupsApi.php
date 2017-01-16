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
class GroupsApi extends AbstractRestfulJsonApi
{
    const ApiPrefix = 'api/v1/groups';

    /**
     * @return bool
     */
    public function checkOwnAjaxRequest()
    {
        $referer = @$_SERVER['HTTP_REFERER'];
        if (empty($referer)) {
            return false;
        }
        //validate
        if (!Director::is_ajax()) {
            return false;
        }
        return Director::is_site_url($referer);
    }


    /**
     * @return bool
     */
    protected function isApiCall()
    {
        $request = $this->getRequest();
        if (is_null($request)) {
            return false;
        }
        return strpos(strtolower($request->getURL()), self::ApiPrefix) !== false;
    }

    /**
     * @return bool
     */
    protected function authorize()
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function authenticate()
    {
        return Permission::check('ADMIN');
    }

    /**
     * @var array
     */
    static $url_handlers = array(
        'GET ' => 'getGroups',
    );

    /**
     * @var array
     */
    static $allowed_actions = array(
        'getGroups',
    );


    /**
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function getGroups(SS_HTTPRequest $request){
        $query_string = $request->getVars();
        $query        = Convert::raw2sql($query_string['query']);
        try{
            $groups = Group::get()->where( " Code LIKE '%{$query}%' ");
            $data = [];
            foreach ($groups as $group) {

                $data[] = [
                    'id'   => $group->ID,
                    'name' => $group->Title
                ];
            }

            return $this->ok($data);
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }
}