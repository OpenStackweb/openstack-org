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

// CMS Pages
GoogleSiteMapGenerator::getInstance()->registerDataObject($class_name = 'SiteTree', $change_freq = GoogleSitemapGenerator::CHANGE_FREQ_MONTHLY, $priority = GoogleSitemapGenerator::PRIORITY_1_0, $get_url_function = function($page){
    return $page->AbsoluteLink();
});

// Marketplace
GoogleSiteMapGenerator::getInstance()->registerDataObject($class_name = 'PublicCloudService', $change_freq = GoogleSitemapGenerator::CHANGE_FREQ_MONTHLY, $priority = GoogleSitemapGenerator::PRIORITY_0_5, $get_url_function = function(IPublicCloudService  $public_cloud){
    $company_slug = $public_cloud->getCompany()->URLSegment;
    $slug         = $public_cloud->getSlug();
    $url          = sprintf('/marketplace/public-clouds/%s/%s', $company_slug , $slug);
    $url          = Director::absoluteURL($url);
    return $url;
});

GoogleSiteMapGenerator::getInstance()->registerDataObject($class_name = 'PrivateCloudService', $change_freq = GoogleSitemapGenerator::CHANGE_FREQ_MONTHLY, $priority = GoogleSitemapGenerator::PRIORITY_0_5, $get_url_function = function(IPrivateCloudService  $private_cloud){
    $company_slug = $private_cloud->getCompany()->URLSegment;
    $slug         = $private_cloud->getSlug();
    $url          = sprintf('/marketplace/hosted-private-clouds/%s/%s', $company_slug , $slug);
    $url          = Director::absoluteURL($url);
    return $url;
});

GoogleSiteMapGenerator::getInstance()->registerDataObject($class_name = 'Appliance', $change_freq = GoogleSitemapGenerator::CHANGE_FREQ_MONTHLY, $priority = GoogleSitemapGenerator::PRIORITY_0_5, $get_url_function = function(IAppliance  $appliance){
    $company_slug = $appliance->getCompany()->URLSegment;
    $slug         = $appliance->getSlug();
    $url          = sprintf('/marketplace/distros/appliance/%s/%s', $company_slug , $slug);
    $url          = Director::absoluteURL($url);
    return $url;
});


GoogleSiteMapGenerator::getInstance()->registerDataObject($class_name = 'Distribution', $change_freq = GoogleSitemapGenerator::CHANGE_FREQ_MONTHLY, $priority = GoogleSitemapGenerator::PRIORITY_0_5, $get_url_function = function(IDistribution  $distro){
    $company_slug = $distro->getCompany()->URLSegment;
    $slug         = $distro->getSlug();
    $url          = sprintf('/marketplace/distros/distribution/%s/%s', $company_slug , $slug);
    $url          = Director::absoluteURL($url);
    return $url;
});

GoogleSiteMapGenerator::getInstance()->registerDataObject($class_name = 'TrainingService', $change_freq = GoogleSitemapGenerator::CHANGE_FREQ_MONTHLY, $priority = GoogleSitemapGenerator::PRIORITY_0_5, $get_url_function = function($training){
    $company_slug = $training->getCompany()->URLSegment;
    $slug         = $training->ID;
    $url          = sprintf('/marketplace/training/%s/%s', $company_slug , $slug);
    $url          = Director::absoluteURL($url);
    return $url;
},
function(){
    $training_facade = new TrainingFacade(
        Controller::curr(),
        new TrainingManager(new SapphireTrainingServiceRepository,
            new SapphireMarketPlaceTypeRepository,
            new TrainingAddPolicy,
            new TrainingShowPolicy,
            new SessionCacheService,
            new MarketplaceFactory,
            SapphireTransactionManager::getInstance()),
        new SapphireCourseRepository(new MarketplaceFactory)
    );

    return $training_facade->getTrainings();
});

// News


GoogleSiteMapGenerator::getInstance()->registerDataObject($class_name = 'News', $change_freq = GoogleSitemapGenerator::CHANGE_FREQ_MONTHLY, $priority = GoogleSitemapGenerator::PRIORITY_0_5, $get_url_function = function(INews $news){

    $id           = $news->ID;
    $slug         = $news->HeadlineForUrl;
    $url          = sprintf('/news/view/%s/%s', $id , $slug);
    $url          = Director::absoluteURL($url);
    return $url;
},
    function(){

        $news_repository = new SapphireNewsRepository();

        $featured_news = $news_repository->getFeaturedNews();
        $recent_news   = $news_repository->getRecentNews();
        $slide_news    = $news_repository->getSlideNews();

        return array_merge($featured_news, $recent_news, $slide_news);
    });

