<?php
/**
 * Copyright 2018 Openstack Foundation
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
 * Object to allow og:image:width
 */
class OGImage implements IMediaFile
{

    public $AbsoluteURL, $Width, $Height, $Type;

    function getWidth(){
        return $this->Width;
    }

    /**
     * Media height in pixels
     *
     * @return integer
     */
    function getHeight(){
        return $this->Height;
    }

    /**
     * Media URL
     *
     * @return string
     */
    function getAbsoluteURL(){
        return $this->AbsoluteURL;
    }

    /**
     * Media mime type
     *
     * @return string
     */
    function getType(){
        return $this->Type;
    }
}