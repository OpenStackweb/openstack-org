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


final class AppLinkIOSMetadataBuilder
{
    public static function buildAppLinksMetaTags(&$tags, $url_path){
        // IOS
        $tags .= sprintf('<meta name="apple-itunes-app" content="app-id=%s, app-argument=%s://%s"/>', APP_LINKS_IOS_APP_STORE_ID, APP_LINKS_IOS_APP_CUSTOM_SCHEMA, $url_path).PHP_EOL;
        $tags .= sprintf('<meta property="al:ios:app_store_id" content="%s" />', APP_LINKS_IOS_APP_STORE_ID).PHP_EOL;
        $tags .= sprintf('<meta property="al:ios:app_name" content="%s" />', APP_LINKS_IOS_APP_NAME).PHP_EOL;
        $tags .= sprintf('<meta property="al:ios:url" content="%s://%s" />', APP_LINKS_IOS_APP_CUSTOM_SCHEMA, $url_path).PHP_EOL;
        $tags .= sprintf('<link rel="alternate" href="ios-app://%s/%s/%s" />', APP_LINKS_IOS_APP_STORE_ID, APP_LINKS_IOS_APP_CUSTOM_SCHEMA, $url_path).PHP_EOL;
    }
}

final class AppLinkIAndroidMetadataBuilder
{
    public static function buildAppLinksMetaTags(&$tags, $url_path){
        // Android
        $tags .= sprintf('<meta property="al:android:package" content="%s" />', APP_LINKS_ANDROID_PACKAGE).PHP_EOL;
        $tags .= sprintf('<meta property="al:android:app_name" content="%s" />', APP_LINKS_ANDROID_APP_NAME).PHP_EOL;
        $tags .= sprintf('<meta property="al:android:url" content="%s://%s" />', APP_LINKS_ANDROID_APP_CUSTOM_SCHEMA, $url_path).PHP_EOL;
        $tags .= sprintf('<link rel="alternate" href="android-app://%s/%s/%s" />', APP_LINKS_ANDROID_PACKAGE, APP_LINKS_ANDROID_APP_CUSTOM_SCHEMA, $url_path).PHP_EOL;
      }
}

class SummitEntityOpenGraphObjectExtension extends OpenGraphObjectExtension
{
    public function MetaTags(&$tags)
    {
        parent::MetaTags($tags);
        $this->buildAppLinksMetaTags($tags);
    }

    private function buildAppLinksMetaTags(&$tags){
        // IOS
        $tags .= AppLinkIOSMetadataBuilder::buildAppLinksMetaTags($tags, sprintf("%s/%s",$this->getEntityPath(), $this->owner->ID));
        // Android
        $tags .= AppLinkIAndroidMetadataBuilder::buildAppLinksMetaTags($tags, sprintf("%s/%s",$this->getEntityPath(), $this->owner->ID));
    }

    protected function getEntityPath(){
        //to implement on child class
        return "";
    }

    public function getOGImage()
    {
        return Director::absoluteURL('/themes/openstack/images/summit/openstacklogo-fb.png');
    }
}