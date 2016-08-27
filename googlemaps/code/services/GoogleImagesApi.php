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
 * Class GoogleImagesService
 */
final class GoogleImagesService extends AbstractRestfulJsonApi
{

    protected function isApiCall()
    {
        $request = $this->getRequest();
        if (is_null($request)) return false;
        return true;
    }

    /**
     * @return bool
     */
    protected function authorize()
    {
        return true;
    }

    protected function authenticate()
    {
        return true;
    }

    static $url_handlers = array(
        'GET pins/$Color!' => 'getGoogleMapPin',
    );

    static $allowed_actions = array
    (
        'getGoogleMapPin',
    );


    public function getGoogleMapPin(SS_HTTPRequest $request)
    {
        $color    =  Convert::raw2sql($request->param('Color'));
        $path     = ASSETS_PATH.'/maps/pins';

        // create folder on assets if does not exists ....
        if(!is_dir($path)){
            mkdir($path, $mode = 0775, $recursive = true);
        }

        // if not get it from google (default)
        $ping_url     = "http://chart.apis.google.com/chart?cht=mm&chs=32x32&chco=FFFFFF,{$color},000000&ext=.png";
        $write_2_disk = true;

        if(file_exists($path.'/pin_'.$color.'.jpg')){
            // if we have the file on assets use it
            $ping_url = $path.'/pin_'.$color.'.jpg';
            $write_2_disk = false;
        }
        $body = file_get_contents($ping_url);
        if($write_2_disk) file_put_contents($path.'/pin_'.$color.'.jpg', $body);
        $ext      = 'jpg';
        $response = new SS_HTTPResponse($body, 200);
        $response->addHeader('Content-Type', 'image/' . $ext);
        return $response;
    }

}