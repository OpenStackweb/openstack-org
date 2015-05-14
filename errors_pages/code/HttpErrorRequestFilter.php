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

class HttpErrorRequestFilter implements RequestFilter {

    /**
     * Filter executed before a request processes
     *
     * @param SS_HTTPRequest $request Request container object
     * @param Session $session Request session
     * @param DataModel $model Current DataModel
     * @return boolean Whether to continue processing other filters. Null or true will continue processing (optional)
     */
    public function preRequest(SS_HTTPRequest $request, Session $session, DataModel $model)
    {
        return true;
    }

    /**
     * Filter executed AFTER a request
     *
     * @param SS_HTTPRequest $request Request container object
     * @param SS_HTTPResponse $response Response output object
     * @param DataModel $model Current DataModel
     * @return boolean Whether to continue processing other filters. Null or true will continue processing (optional)
     */
    public function postRequest(SS_HTTPRequest $request, SS_HTTPResponse $response, DataModel $model)
    {
        $code = $response->getStatusCode();
        $error_page_path = Director::baseFolder()."/errors_pages/ui/{$code}/index.html";
        if(file_exists($error_page_path)){
            $page_file   = fopen($error_page_path, "r") or die("Unable to open file!");
            $body        = fread($page_file,filesize($error_page_path));
            fclose($page_file);
            $response->setBody($body);
            $response->setStatusCode(200);
            return true;
        }
        return true;
    }
}