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
    public static function buildTwitterCardMetaTags(&$tags){
        // IOS
        $tags .= '<meta name="twitter:card" content="summary" />'.PHP_EOL;
        $tags .= sprintf('<meta name="twitter:site" content="%s" />', OPENSTACK_TWITTER_ACCOUNT).PHP_EOL;
    }
}

class PageOpenGraphObjectExtension extends OpenGraphObjectExtension
{
    public static $default_image = '/themes/openstack/images/openstack-logo-full.png';

    public function MetaTags(&$tags)
    {
        parent::MetaTags($tags);
        $this->buildAppLinksMetaTags($tags);
    }

    protected function buildAppLinksMetaTags(&$tags){

        TwitterCardMetadataBuilder::buildTwitterCardMetaTags($tags);
    }

    public function getMetaValue($field_name) {
        $field_value = '';
        $parent = $this->owner->Parent();

        if ($this->owner->hasMethod($field_name)) {
            if ($field_name == 'MetaImage' && $this->owner->MetaImage()->Exists()) {
                return $this->owner->MetaImage();
            } else if ($parent->getField($field_name)) {
                return $this->owner->getField($field_name);
            }
        }

        while( $parent && $parent->exists() ) {
            if ($parent->hasMethod($field_name)) {
                if ($field_name == 'MetaImage' && $parent->MetaImage()->Exists()) {
                    $field_value = $parent->$field_name();
                    break;
                } else if ($parent->getField($field_name)) {
                    $field_value = $parent->getField($field_name);
                    break;
                }
            }
            $parent = $parent->Parent();
        }

        return $field_value;
    }

    public function getOGImage()
    {
        $meta_image = $this->getMetaValue('MetaImage');
        if ($meta_image && $meta_image->Exists())
            return $meta_image->getURL();

        return Director::absoluteURL(self::$default_image);
    }

    public function getOGTitle()
    {
        $meta_title = $this->getMetaValue('MetaTitle');

        if($meta_title) {
            $title = trim($meta_title);
            if(!empty($title)) return $title;
        }

        return $this->owner->Title." - OpenStack Open Source Cloud Computing Software";
    }

}