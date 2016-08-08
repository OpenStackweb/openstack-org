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

class PageOpenGraphObjectExtension extends OpenGraphObjectExtension
{
    public static $default_image = '/themes/openstack/images/openstack-logo-full.png';

    public function getOGImage()
    {
        if ($this->owner->hasField('MetaImage') && $this->owner->MetaImage()->Exists())
            return $this->owner->MetaImage()->getURL();
        return Director::absoluteURL(self::$default_image);
    }

    public function getOGTitle()
    {
        if($this->owner->hasField('MetaTitle')) {
            $title = trim($this->owner->MetaTitle);
            if(!empty($title)) return $title;
        }

        return $this->owner->Title." &raquo; OpenStack Open Source Cloud Computing Software";
    }

}