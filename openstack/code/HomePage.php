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
 
class HomePage extends Page {
   static $db = array(
		'FeedData' => 'HTMLText',
		'EventDate' => 'Date',
		'VideoCurrentlyPlaying' => 'Text'
   );

   static $has_one = array(
   );

	function getCMSFields() {
		$fields = parent::getCMSFields();

		// Summit Video Stream
      	$VideoLiveField = new OptionSetField('VideoCurrentlyPlaying', 'Is the video live streaming at the moment?', array(
            'Yes' => 'Video is being streamed.',
            'No' => 'No video playing.'
        ));

		$fields->addFieldToTab("Root.Main", $VideoLiveField, 'Content');

		// Countdown Date
		$EventStartDate = new DateField('EventDate','First Day of Event (for counting down)');
		$EventStartDate->setConfig('showcalendar', true);
		$EventStartDate->setConfig('showdropdown', true);
		$fields->addFieldToTab('Root.Main', $EventStartDate, 'Content');

		// remove unneeded fields 
		$fields->removeFieldFromTab("Root.Main","Content");

		return $fields;
	}   
}
 
class HomePage_Controller extends Page_Controller {

	static $allowed_actions = array(
		'Video',
		'LatestNews'
	);	
			
	function init() { 
	   parent::init(); 
	       	       
		//	Set default currency unless this is a returning visitor 
	   $VisitorCookie = new Cookie; 
	   if(!$VisitorCookie->get('ReturningVisitor')) { 
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
            $event_main_info = new EventMainInfo(html_entity_decode($item->title),$item->link,'Details');
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
                $data = array('IsEmpty'=>0,'IsFirst'=>$first);

                $output .= $event->renderWith('EventHolder_event', $data);
            }
        } else {
            $data = array('IsEmpty'=>1);
            $output .= Page::renderWith('EventHolder_event', $data);
        }

        return $output;
	}

	function DisplayVideo() {
		$getVars = $this->request->getVars();
		return ($this->VideoCurrentlyPlaying == 'Yes' || isset($getVars['video']));
	}

	function Video() {
		//Detect special conditions devices
		$iPod = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
		$iPhone = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
		$iPad = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");

		//do something with this information
		if( $iPod || $iPhone ||  $iPad ){
		    $this->redirect('http://itechsherpalive2.live-s.cdn.bitgravity.com/cdn-live-s1/_definst_/itechsherpalive2/live/OSS13/playlist.m3u8');
		} else {
			return $this->renderWith(array('HomePage_Video','HomePage','Page'));
		}

	}

    function NewsItems($limit = 20) {
        $return_array = new ArrayList();
        $slider_news = DataObject::get('News', "Slider = 1", "Rank ASC,Date DESC", "", $limit)->toArray();
        $limit = $limit - count($slider_news);
        $featured_news = DataObject::get('News', "Featured = 1", "Rank ASC,Date DESC", "", $limit)->toArray();
        $limit = $limit - count($featured_news);
        $recent_news = DataObject::get('News', "Featured = 0 AND Slider = 0 AND Approved = 1", "Rank ASC,Date DESC", "", $limit)->toArray();
        $limit = $limit - count($recent_news);
        $all_news = array_merge($slider_news,$featured_news,$recent_news);
        // format array
        foreach ($all_news as $item) {
            $art_link = 'news/view/'.$item->ID.'/'.$item->HeadlineForUrl;
            $return_array->push(array('type'=>'News','link'=>$art_link,'title'=>$item->Headline,
                                      'pubdate'=>date('D, M jS Y',strtotime($item->Date)),'timestamp'=>strtotime($item->Date)));
        }

        $rss_news = $this->RssItems($limit)->toArray();
        foreach ($rss_news as $item) {
            $date_obj = DateTime::createFromFormat('D, M jS Y', $item->pubDate);
            $return_array->push(array('type' => 'Planet', 'link' => $item->link, 'title' => $item->title,
                'pubdate' => $item->pubDate, 'timestamp' => $date_obj->getTimestamp()));
        }

        $blog_news = $this->RssItems($limit)->toArray();
        foreach ($blog_news as $item) {
            $date_obj = DateTime::createFromFormat('D, M jS Y', $item->pubDate);
            $return_array->push(array('type' => 'Blog', 'link' => $item->link, 'title' => $item->title,
                'pubdate' => $item->pubDate, 'timestamp' => $date_obj->getTimestamp()));
        }

        $superuser_news = $this->SuperUserItems($limit)->toArray();
        foreach ($superuser_news as $item) {
            $date_obj = DateTime::createFromFormat('D, M jS Y', $item->pubDate);
            $return_array->push(array('type' => 'SuperUser', 'link' => $item->link, 'title' => $item->title,
                'pubdate' => $item->pubDate, 'timestamp' => $date_obj->getTimestamp()));
        }

        $return_array = $return_array->sort('timestamp', 'DESC');
        return $return_array->limit($limit,0);
    }

    function RssItems($limit = 7)
    {

        $feed = new RestfulService('http://planet.openstack.org/rss20.xml', 7200);

        $feedXML = $feed->request()->getBody();

        // Extract items from feed
        $result = $feed->getValues($feedXML, 'channel', 'item');

        foreach ($result as $item) {
            $item->pubDate = date("D, M jS Y", strtotime($item->pubDate));
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
            $item->pubDate = date("D, M jS Y", strtotime($item->pubDate));
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
            $item->pubDate = date("D, M jS Y", strtotime($item->published));
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