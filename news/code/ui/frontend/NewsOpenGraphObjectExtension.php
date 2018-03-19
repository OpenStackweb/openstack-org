<?php
/**
 * Copyright 2018 OpenStack Foundation
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

class NewsOpenGraphObjectExtension extends OpenGraphObjectExtension
{
    public function MetaTags(&$tags)
    {
        parent::MetaTags($tags);
        TwitterCardMetadataBuilder::buildTwitterCardMetaTags($tags);
    }

    public function getOGDescription()
    {
        return strip_tags($this->owner->Summary);
    }

    public function getOGImage()
    {
        $image = $this->owner->Image();
        $default_image = Director::absoluteURL('/themes/openstack/images/summit/openstacklogo-fb.png');

        return $image->Exists() ? Director::absoluteURL($image->getURL()) : $default_image;
    }

    public function getOGTitle()
    {
        return $this->owner->Headline;
    }
}