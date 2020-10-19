<?php
/**
 * Copyright 2018 Open Infrastructure Foundation
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
        $default_image = Director::absoluteURL(CloudAssetTemplateHelpers::cloud_url('images/summit/openstacklogo-fb.png'));

        $og_image = new OGImage();
        $og_image->Width = 200;
        $og_image->Height = 200;
        $og_image->AbsoluteURL = Director::absoluteURL($image->getURL());

        return $image->Exists() ? $og_image : $default_image;
    }

    public function getOGTitle()
    {
        return $this->owner->Headline;
    }
}