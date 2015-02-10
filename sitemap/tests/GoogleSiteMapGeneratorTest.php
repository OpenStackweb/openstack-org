<?php
/**
 * Copyright 2014 Openstack Foundation
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
 * Class GoogleSiteMapGeneratorTest
 */
class GoogleSiteMapGeneratorTest extends SapphireTest {

    protected static $fixture_file = 'GoogleSiteMapGeneratorTest.yml';

    /*public function testRegisterDataObject(){

        GoogleSiteMapGenerator::getInstance()->registerDataObject($class_name = 'PublicCloudService', $change_freq = GoogleSiteMapGenerator::CHANGE_FREQ_MONTHLY, $priority = GoogleSiteMapGenerator::PRIORITY_1_0, $get_url_function = function(IPublicCloudService  $public_cloud){
            $company_name = $public_cloud->getCompany()->getName();
            $slug         = $public_cloud->getSlug();
            $url          = sprintf('/marketplace/public-clouds/%s/%s', $company_name , $slug);
            $url          =  Director::absoluteURL($url);
            return $url;
        });

        $this->assertTrue(GoogleSiteMapGenerator::getInstance()->isRegisteredDataObject($class_name = 'PublicCloudService' ));
    }*/

    public function testGetEntries(){

        GoogleSiteMapGenerator::getInstance()->registerDataObject($class_name = 'SiteTree', $change_freq = GoogleSitemapGenerator::CHANGE_FREQ_MONTHLY, $priority = GoogleSitemapGenerator::PRIORITY_1_0, $get_url_function = function($page){
            $url          = $page->AbsoluteLink();
            return $url;
        });

        GoogleSiteMapGenerator::getInstance()->registerDataObject($class_name = 'PublicCloudService', $change_freq = GoogleSitemapGenerator::CHANGE_FREQ_MONTHLY, $priority = GoogleSitemapGenerator::PRIORITY_1_0, $get_url_function = function(IPublicCloudService  $public_cloud){
            $company_name = $public_cloud->getCompany()->getName();
            $slug         = $public_cloud->getSlug();
            $url          = sprintf('/marketplace/public-clouds/%s/%s', $company_name , $slug);
            $url          = Director::absoluteURL($url);
            return $url;
        });

        $list = GoogleSiteMapGenerator::getInstance()->Entries();

        $this->assertTrue(count($list) > 0);
    }


}