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

        $fields->addFieldToTab("Root.Main", $VideoLiveField, 'Content');

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

    const PromoIntroMessageDefault = '"OpenStack has a true community around it."';
    const PromoButtonTextDefault   = 'See how @WalmartLabs puts 100,000 cores to work';
    const PromoButtonUrlDefault = 'http://awe.sm/jM31y';
    const PromoDatesTextDefault = '...we plan to contribute aggressively to the open source community.';
    const PromoHeroCreditDefault = 'Photo by Claire Massey';
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
        if(isset($getVars['summit'])) $this->VideoCurrentlyPlaying = 'Yes';

        if ($this->SummitMode == 'Yes' || isset($getVars['summit'])) {
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

        Requirements::customScript("Shadowbox.init();");

    }

    function RssEvents($limit = 7)
    {

        $feed = new RestfulService('https://groups.openstack.org/events-upcoming.xml', 7200);

        $feedXML = $feed->request()->getBody();

        // Extract items from feed
        $result = $feed->getValues($feedXML, 'channel', 'item');

        foreach ($result as $item) {
            $item->pubDate = date("D, M jS Y", strtotime($item->pubDate));
            $DOM = new DOMDocument;
            $DOM->loadHTML(html_entity_decode($item->description));
            $span_tags = $DOM->getElementsByTagName('span');
            foreach ($span_tags as $tag) {
                if ($tag->getAttribute('property') == 'schema:startDate') {
                    $item->startDate = $tag->getAttribute('content');
                } else if ($tag->getAttribute('property') == 'schema:endDate') {
                    $item->endDate = $tag->getAttribute('content');
                }
            }
            $div_tags = $DOM->getElementsByTagName('div');
            foreach ($div_tags as $tag) {
                if ($tag->getAttribute('property') == 'schema:location') {
                    $item->location = $tag->nodeValue;
                }
            }
        }

        return $result->limit($limit, 0);
    }

    function UpcomingEvents($limit = 1)
    {
        $rss_events = $this->RssEvents($limit);
        $events_array = new ArrayList();
        $pulled_events = EventPage::get()->where("EventEndDate >= now()")->sort('EventStartDate', 'ASC')->limit($limit)->toArray();
        $events_array->merge($pulled_events);
        $output = '';

        foreach ($rss_events as $item) {
            $event_main_info = new EventMainInfo(html_entity_decode($item->title),$item->link,'Details','Meetups');
            $event_start_date = DateTime::createFromFormat(DateTime::ISO8601, $item->startDate);
            $event_end_date = DateTime::createFromFormat(DateTime::ISO8601, $item->endDate);
            $event_duration = new EventDuration($event_start_date,$event_end_date);
            $event = new EventPage();
            $event->registerMainInfo($event_main_info);
            $event->registerDuration($event_duration);
            $event->registerLocation($item->location);
            $events_array->push($event);
        }

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
        $return_array = new ArrayList();
        $outsourced_limit = 5;
        $local_limit = $limit - $outsourced_limit;

        $rss_news = $this->RssItems($outsourced_limit)->toArray();
        foreach ($rss_news as $item) {
            $return_array->push(array('type' => 'Planet', 'link' => $item->link, 'title' => $item->title,
                'pubdate' => $item->date_display, 'timestamp' => $item->timestamp));
        }

        $blog_news = $this->BlogItems($outsourced_limit)->toArray();
        foreach ($blog_news as $item) {
            $return_array->push(array('type' => 'Blog', 'link' => $item->link, 'title' => $item->title,
                'pubdate' => $item->date_display, 'timestamp' => $item->timestamp));
        }

        $superuser_news = $this->SuperUserItems($outsourced_limit)->toArray();
        foreach ($superuser_news as $item) {
            $return_array->push(array('type' => 'Superuser', 'link' => $item->link, 'title' => $item->title,
                'pubdate' => $item->date_display, 'timestamp' => $item->timestamp));
        }

        $return_array = $return_array->sort('timestamp', 'DESC')->limit($outsourced_limit,0);

        $openstack_news = DataObject::get('News', "Approved = 1", "Date DESC", "", $local_limit)->toArray();
        foreach ($openstack_news as $item) {
            $art_link = 'news/view/' . $item->ID . '/' . $item->HeadlineForUrl;
            $return_array->push(array('type' => 'News', 'link' => $art_link, 'title' => $item->Headline,
                'pubdate' => date('D, M jS Y', strtotime($item->Date)), 'timestamp' => strtotime($item->Date)));
        }

        return $return_array->sort('timestamp', 'DESC')->limit($limit,0);
    }

    function RssItems($limit = 7)
    {

        $feed = new RestfulService('http://planet.openstack.org/rss20.xml', 7200);

        $feedXML = $feed->request()->getBody();

        // Extract items from feed
        $result = $feed->getValues($feedXML, 'channel', 'item');

        foreach ($result as $item) {
            $item->date_display = date("D, M jS Y", strtotime($item->pubDate));
            $item->timestamp = strtotime($item->pubDate);
        }

        return $result->limit($limit, 0);
    }

    function BlogItems($limit = 7)
    {

        $feed = new RestfulService('https://www.openstack.org/blog/feed/', 7200);

        $feedXML = $feed->request()->getBody();

        // Extract items from feed
        $result = $feed->getValues($feedXML, 'channel', 'item');

        foreach ($result as $item) {
            $item->date_display = date("D, M jS Y", strtotime($item->pubDate));
            $item->timestamp = strtotime($item->pubDate);
        }

        return $result->limit($limit, 0);
    }

    function SuperUserItems($limit = 7)
    {

        $feed = new RestfulService('http://superuser.openstack.org/articles/feed/', 7200);

        $feedXML = $feed->request()->getBody();

        // Extract items from feed
        $result = $feed->getValues($feedXML, 'entry');

        foreach ($result as $item) {
            $item->date_display = date("D, M jS Y", strtotime($item->published));
            $item->timestamp = strtotime($item->published);
        }

        return $result->limit($limit, 0);
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