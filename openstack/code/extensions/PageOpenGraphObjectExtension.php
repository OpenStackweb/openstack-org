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


final class TwitterCardMetadataBuilder
{
    public static function buildTwitterCardMetaTags(&$tags, $object){
        // IOS
        $tags .= '<meta name="twitter:card" content="summary" />'.PHP_EOL;
        $tags .= sprintf('<meta name="twitter:site" content="%s" />', OPENSTACK_TWITTER_ACCOUNT).PHP_EOL;
        $tags .= sprintf('<meta name="twitter:title" content="%s" />', $object->getOGTitle()).PHP_EOL;
        $tags .= sprintf('<meta name="twitter:description" content="%s" />', $object->getOGDescription()).PHP_EOL;
        $tags .= sprintf('<meta name="twitter:image" content="%s" />', $object->getOGImage()).PHP_EOL;
    }
}

class PageOpenGraphObjectExtension extends OpenGraphObjectExtension
{
    public function MetaTags(&$tags)
    {
        parent::MetaTags($tags);
        $this->buildAppLinksMetaTags($tags);
    }

    private function buildAppLinksMetaTags(&$tags){

        TwitterCardMetadataBuilder::buildTwitterCardMetaTags($tags, $this->owner);
    }

}