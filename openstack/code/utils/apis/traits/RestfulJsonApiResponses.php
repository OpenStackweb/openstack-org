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

/**
 * Class RestfulJsonApiResponses
 */
trait RestfulJsonApiResponses
{
    protected function ok(array $res = null, $use_etag = true)
    {
        $response = new SS_HTTPResponse();
        $response->setStatusCode(200);
        $response->addHeader('Content-Type', 'application/json');
        if (is_null($res)) {
            $res = array();
        }
        $response->setBody(json_encode($res));

        //conditional get Request (etags)
        $request = Controller::curr()->getRequest();
        if ($request->isGET() && $use_etag) {
            $etag = md5($response->getBody());
            $requestETag = $request->getHeader('If-None-Match');
            foreach (array(
                         'Expires',
                         'Cache-Control'
                     ) as $header) {
                $response->removeHeader($header);
            }

            $lastmod = gmdate('D, d M Y 0:0:0 \G\M\T', time());
            $response->addHeader('Cache-Control', 'max-age=3600');
            $response->addHeader('Last-Modified', $lastmod);
            $response->addHeader('Expires', gmdate('D, d M Y H:m:i \G\M\T', time() + 3600));
            $response->addHeader('ETag', $etag);
            if (!empty($requestETag) && $requestETag == $etag) {
                $response->setStatusCode(304);
                $response->addHeader('ETag', $etag);
                $response->setBody(null);
            }
        }
        return $response;
    }

    protected function deleted()
    {
        $response = new SS_HTTPResponse();
        $response->setStatusCode(204);
        $response->addHeader('Content-Type', 'application/json');
        $response->setBody('');

        return $response;
    }

    protected function updated()
    {
        $response = new SS_HTTPResponse();
        $response->setStatusCode(204);
        $response->addHeader('Content-Type', 'application/json');
        $response->setBody('');

        return $response;
    }

    protected function published()
    {
        $response = new SS_HTTPResponse();
        $response->setStatusCode(204);
        $response->addHeader('Content-Type', 'application/json');
        $response->setBody('');

        return $response;
    }

    public function serverError()
    {
        $response = new SS_HTTPResponse();
        $response->setStatusCode(500);
        $response->addHeader('Content-Type', 'application/json');
        $response->setBody(json_encode("Server Error"));

        return $response;
    }

    public function forbiddenError()
    {
        $response = new SS_HTTPResponse();
        $response->setStatusCode(403);
        $response->addHeader('Content-Type', 'application/json');
        $response->setBody(json_encode("Security Error"));

        return $response;
    }

    public function validationError($messages)
    {
        $response = new SS_HTTPResponse();
        $response->setStatusCode(412);
        $response->addHeader('Content-Type', 'application/json');
        if (!is_array($messages)) {
            $messages = array(array('message' => $messages));
        }
        $response->setBody(json_encode(
            array('error' => 'validation', 'messages' => $messages)
        ));

        return $response;
    }

    protected function created($id)
    {
        $response = new SS_HTTPResponse();
        $response->setStatusCode(201);
        $response->addHeader('Content-Type', 'application/json');
        $response->setBody(json_encode($id));

        return $response;
    }


    protected function methodNotAllowed()
    {
        $response = new SS_HTTPResponse();
        $response->setStatusCode(405);
        $response->addHeader('Content-Type', 'application/json');
        $response->setBody(json_encode("Method Not Allowed"));

        return $response;
    }

    public function permissionFailure()
    {
        // return a 401
        $response = new SS_HTTPResponse();
        $response->setStatusCode(401);
        $response->addHeader('Content-Type', 'application/json');
        $response->setBody(json_encode("You don't have access to this item through the API."));

        return $response;
    }

    protected function addingDuplicate($msg)
    {
        // return a 401
        $response = new SS_HTTPResponse();
        $response->setStatusCode(409);
        $response->addHeader('Content-Type', 'application/json');
        $response->setBody(json_encode($msg));

        return $response;
    }

    public function checkOwnAjaxRequest(){
        $referer = @$_SERVER['HTTP_REFERER'];
        if(empty($referer)) return false;
        //validate
        if (!Director::is_ajax()) return false;
        return Director::is_site_url($referer);
    }
}