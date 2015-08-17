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
 * testing
 **/

/**
 * Defines the HomePage page type
 */
class HomePage extends Page
{

    static $db = array(
        'FeedData' => 'HTMLText',
        'EventDate' => 'Date',
        'VideoCurrentlyPlaying' => 'Text',
        'PromoIntroMessage' => 'Text',
        'PromoButtonText' => 'Text',
        'PromoButtonUrl' => 'Text',
        "PromoDatesText" => 'Text',
        "PromoHeroCredit" => 'Text',
        "PromoHeroCreditUrl" => 'Text',
        "SummitMode" => 'Boolean',
        "NextPresentationStartTime" => 'HTMLText',
        "NextPresentationStartDate" => 'Text',
        "LiveStreamURL" => 'Text'
    );

    private static $has_one  = array(
        'PromoImage' => 'BetterImage',
    );

    function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // Summit Video Stream
        $VideoLiveField = new OptionSetField('VideoCurrentlyPlaying', 'Is the video live streaming at the moment?', array(
            'Yes' => 'Video is being streamed.',
            'No' => 'No video playing.'
        ));

        $fields->addFieldToTab("Root.Main", new TextField('LiveStreamURL', 'URL of Live Stream Feed'));

        $SummitModeField = new OptionSetField('SummitMode', 'Homepage mode:', array(
            0 => 'Normal Mode',
            1 => 'Summit Mode'
        ));

        $fields->addFieldToTab("Root.Main", new TextField('NextPresentationStartTime', 'Next Presentation Start Time'));
        $fields->addFieldToTab("Root.Main", new TextField('NextPresentationStartDate', 'Next Presentation Start Date'));        

        $fields->addFieldToTab("Root.Main", $VideoLiveField, 'Content');
        $fields->addFieldToTab("Root.Main", $SummitModeField, 'Content');

        // Countdown Date
        $EventStartDate = new DateField('EventDate', 'First Day of Event (for counting down)');
        $EventStartDate->setConfig('showcalendar', true);
        $EventStartDate->setConfig('showdropdown', true);
        $fields->addFieldToTab('Root.Main', $EventStartDate, 'Content');

        // remove unneeded fields
        $fields->removeFieldFromTab("Root.Main", "Content");

        $promo_hero_image  = new CustomUploadField('PromoImage', 'Promo Hero Image');
        $promo_hero_image->setFolderName('homepage');
        $promo_hero_image->setAllowedFileCategories('image');

        $fields->addFieldToTab("Root.IntroHeader", $promo_hero_image);
        $fields->addFieldToTab("Root.IntroHeader", new TextareaField('PromoIntroMessage', 'Promo Intro Message'));
        $fields->addFieldToTab("Root.IntroHeader", new TextareaField('PromoButtonText', 'Promo Button Text'));
        $fields->addFieldToTab("Root.IntroHeader", new TextareaField('PromoButtonUrl', 'Promo Button Url'));
        $fields->addFieldToTab("Root.IntroHeader", new TextareaField('PromoDatesText', 'Promo Dates Text'));
        $fields->addFieldToTab("Root.IntroHeader", new TextareaField('PromoHeroCredit', 'Hero Credit'));
        $fields->addFieldToTab("Root.IntroHeader", new TextareaField('PromoHeroCreditUrl', 'Hero Credit Url'));
        return $fields;
    }

    public function getHeroImageUrl(){
        $image = $this->PromoImage();
        if(!is_null($image) && $image->exists())
            return $image->Link();
        return '/assets/homepage/homepage-parissummit.png';
    }

    public function getPromoIntroMessage(){
        $value = $this->getField('PromoIntroMessage');
        return !empty($value)? $value : self::PromoIntroMessageDefault;
    }

    public function getPromoButtonText(){
        $value = $this->getField('PromoButtonText');
        return !empty($value)? $value : self::PromoButtonTextDefault;
    }

    public function getPromoButtonUrl(){
        $value = $this->getField('PromoButtonUrl');
        return !empty($value)? $value : self::PromoButtonUrlDefault;
    }

    public function getPromoDatesText(){
        $value = $this->getField('PromoDatesText');
        return !empty($value)? $value : self::PromoDatesTextDefault;
    }

    public function getPromoHeroCredit(){
        $value = $this->getField('PromoHeroCredit');
        return !empty($value)? $value : self::PromoHeroCreditDefault;
    }

    public function getPromoHeroCreditUrl(){
        $value = $this->getField('PromoHeroCreditUrl');
        return !empty($value)? $value : self::PromoHeroCreditUrlDefault;
    }

    const PromoIntroMessageDefault = '"OpenStack has a true community around it."';
    const PromoButtonTextDefault   = 'See how @WalmartLabs puts 100,000 cores to work';
    const PromoButtonUrlDefault = 'http://awe.sm/jM31y';
    const PromoDatesTextDefault = '...we plan to contribute aggressively to the open source community.';
    const PromoHeroCreditDefault = 'Photo by Claire Massey';
    const PromoHeroCreditUrlDefault = 'https://www.flickr.com/groups/1574695@N22/';
}

class HomePage_Controller extends Page_Controller
{

    static $allowed_actions = array(
        'Video',
        'LatestNews',
        'handleIndex'
    );

    static $url_handlers = array(
        '' => 'handleIndex',
    );

    // checks to see if the hompeage is in summit mode (if so, changes template used)
    public function handleIndex(){
        $getVars = $this->request->getVars(); 

        // turn the video on if set in a URL parameter
        if(isset($getVars['video'])) $this->VideoCurrentlyPlaying = 'Yes';

        if ($this->SummitMode == TRUE || isset($getVars['summit'])) {
            return $this->renderWith(array('HomePage_Summit', 'HomePage', 'Page'));
        } else {
            return $this;
        }
    }

    function init()
    {
        parent::init();

        //	Set default currency unless this is a returning visitor
        $VisitorCookie = new Cookie;
        if (!$VisitorCookie->get('ReturningVisitor')) {
            $VisitorCookie->set('ReturningVisitor', TRUE);
        }

    }


    /**
     * @param string $url
     * @param int  $expiry
     * @param null $collection
     * @param null $element
     * @return ArrayList
     */
    private function queryExternalSource($url, $expiry=3600, $collection = NULL, $element = NULL){
        $output = new ArrayList();
        try {
            $feed     = new RestfulService($url, $expiry);
            $response = $feed->request();
            if ($response->getStatusCode() == 200) {
                $body = $response->getBody();
                $output = $feed->getValues($body, $collection, $element);
            }
        }
        catch(Exception $ex){
            SS_Log::log($ex, SS_Log::WARN);
        }
        return $output;
    }

    function UpcomingEvents($limit = 1)
    {

        $events_array = new ArrayList();
        Versioned::reading_stage('Live');
        $pulled_events = EventPage::get()->where("EventEndDate >= now()")->sort('EventStartDate', 'ASC')->limit($limit)->toArray();
        $events_array->merge($pulled_events);
        $output = '';
        $events = $events_array->sort('EventStartDate', 'ASC')->limit($limit,0)->toArray();

        if ($events) {
            foreach ($events as $key => $event) {
                $first = ($key == 0);
                $data = array('IsEmpty' => 0, 'IsFirst' => $first);

                $output .= $event->renderWith('EventHolder_event', $data);
            }
        } else {
            $data = array('IsEmpty' => 1);
            $event = new EventPage();
            $output .= $event->renderWith('EventHolder_event', $data);
        }

        return $output;
    }

    function DisplayVideo()
    {
        $getVars = $this->request->getVars();
        return ($this->VideoCurrentlyPlaying == 'Yes' || isset($getVars['video']));
    }

    function Video()
    {
        //Detect special conditions devices
        $iPod = stripos($_SERVER['HTTP_USER_AGENT'], "iPod");
        $iPhone = stripos($_SERVER['HTTP_USER_AGENT'], "iPhone");
        $iPad = stripos($_SERVER['HTTP_USER_AGENT'], "iPad");

        //do something with this information
        if ($iPod || $iPhone || $iPad) {
            $this->redirect('http://itechsherpalive2.live-s.cdn.bitgravity.com/cdn-live-s1/_definst_/itechsherpalive2/live/OSS13/playlist.m3u8');
        } else {
            return $this->renderWith(array('HomePage_Video', 'HomePage', 'Page'));
        }

    }

    function NewsItems($limit = 20)
    {
        $repository = new SapphireRssNewsRepository();
        $tx_manager = SapphireTransactionManager::getInstance();
        $rss_news_manager = new RssNewsManager(
            $repository,
            $tx_manager
        );

        $return_array = new ArrayList();

        $group_array = $rss_news_manager->getNewsItemsFromDatabaseGroupedByCategory();

        for ($i = 0; $i < 7 && $i < count($group_array[RssNews::SuperUser]); $i++ ) {
            $item = $group_array[RssNews::SuperUser][$i];
            $return_array->push(array('type' => $item->Category, 'link' => $item->Link, 'title' => $item->Headline,
                'pubdate' => date('D, M jS Y', strtotime($item->Date)), 'rawpubdate' => $item->Date));
        }

        for ($i = 0; $i < 3 && $i < count($group_array[RssNews::Planet]); $i++ ) {
            $item = $group_array[RssNews::Planet][$i];

            $return_array->push(array('type' => $item->Category, 'link' => $item->Link, 'title' => $item->Headline,
                'pubdate' => date('D, M jS Y', strtotime($item->Date)), 'rawpubdate' => $item->Date));
        }

        /*for ($i = 0; $i < 3 && $i < count($group_array[RssNews::Blog]); $i++ ) {
            $item = $group_array[RssNews::Blog][$i];

            $return_array->push(array('type' => $item->Category, 'link' => $item->Link, 'title' => $item->Headline,
                'pubdate' => date('D, M jS Y', strtotime($item->Date)), 'rawpubdate' => $item->Date));
        }*/

        $rss_count = $return_array->count();

        $openstack_news = DataObject::get('News', "Approved = 1", "Date DESC", "", $limit - $rss_count)->toArray();
        foreach ($openstack_news as $item) {
            $art_link = 'news/view/' . $item->ID . '/' . $item->HeadlineForUrl;
            $return_array->push(array('type' => 'News', 'link' => $art_link, 'title' => $item->Headline,
                'pubdate' => date('D, M jS Y', strtotime($item->Date)), 'rawpubdate' => $item->Date));
        }

        return $return_array->sort('rawpubdate', 'DESC')->limit($limit,0);
    }

    function PastEvents($num = 1)
    {
        return EventPage::get()->where("EventEndDate <= now()")->sort('EventStartDate')->limit($num);
    }

    function ReturningVisitor()
    {
        $VisitorCookie = new Cookie;
        return ($VisitorCookie->get('ReturningVisitor') == TRUE);
    }

    function CompanyCount()
    {
        $DisplayedCompanies = Company::get()->filter('DisplayOnSite', 1);
        $Count = $DisplayedCompanies->Count();
        return $Count;
    }

    function DaysUntil()
    {
        $date = $this->EventDate;
        return (isset($date)) ? floor((strtotime($date) - time()) / 60 / 60 / 24) : FALSE;
    }
}
