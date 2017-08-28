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
class SangriaApi extends AbstractRestfulJsonApi
{
    private static $api_prefix = 'api/v1/sangria';

    /**
     * @return bool
     */
    protected function authorize()
    {
        //check permissions
        if ($this->request->param('SUBROUTE') == SangriaSurveyBuilderRouterApiExtension::SubRouteSurveyTemplates) {
            if(!Permission::check("FREE_TEXT_TAGGING_ACCESS"))
                return false;
        } else {
            if(!Permission::check("SANGRIA_ACCESS"))
                return false;
        }

        return true;
    }

    static $url_handlers = [
        '$SUBROUTE' => 'handleSubRoute',
    ];

    static $allowed_actions = [
        'handleSubRoute'
    ];

    /**
     * @param SS_HTTPRequest $request
     * @return array|mixed|SS_HTTPResponse
     */
    public function handleSubRoute(SS_HTTPRequest $request){
        // @see https://docs.silverstripe.org/en/3/developer_guides/extending/extensions/

        $subroute = $request->param('SUBROUTE');
        $response = new SS_HTTPResponse('route not found', 404);
        $res      = $this->extend('handleSubRoute', $subroute, $request);

        if(!is_null($res) && is_array($res) && count($res) > 0){
            $res = $res[0];
            if($res instanceof SS_HTTPResponse)
                return $res;
        }

        return $response;
    }
}