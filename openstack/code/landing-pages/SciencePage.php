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
class SciencePage extends Page {
    static $db = array(
        'AmazonLink'    => 'Varchar(255)'
	);

    static $has_one = array(
        'BookPDF'   => 'CloudFile',
        'PrintPDF'  => 'CloudFile'
    );

    function getCMSFields()
    {
        $fields = parent::getCMSFields();
        // remove unneeded fields
        $fields->removeFieldFromTab("Root.Main", "Content");

        $fields->addFieldToTab("Root.Main", $book = UploadField::create('BookPDF','Book PDF'));
        $book->setFolderName('science');
        $book->getValidator()->setAllowedMaxFileSize(40*1024*1024);
        $fields->addFieldToTab("Root.Main", $print = UploadField::create('PrintPDF','Print PDF'));
        $print->setFolderName('science');
        $print->getValidator()->setAllowedMaxFileSize(40*1024*1024);
        $fields->addFieldToTab("Root.Main", new TextField('AmazonLink','Amazon Link'));

        return $fields;
    }
}
 
class SciencePage_Controller extends Page_Controller {

    function init()
    {
        parent::init();

        Requirements::CSS('themes/openstack/css/enterprise.css');
        Requirements::CSS('themes/openstack/css/science.css');

        Requirements::javascript('themes/openstack/javascript/enterprise.js');

    }

    public function getEnterpriseEvents($limit = 3)
    {
        $next_summit = $this->getSummitEvent();
        $filter = array("EventEndDate:GreaterThan" => date('Y-m-d H:i:s'), "ID:not" => $next_summit->ID);
        return EventPage::get()
            ->where("EventCategory IN('Enterprise','Summit')")
            ->filter($filter)
            ->sort('EventStartDate')
            ->limit($limit);
    }

    public function getEnterpriseFeaturedEvents($limit = 3)
    {
        $next_summit = $this->getSummitEvent();
        $filter = array("EventEndDate:GreaterThan" => date('Y-m-d H:i:s'), "ID:not" => $next_summit->ID);
        return EventPage::get()
            ->where("EventCategory IN('Enterprise','Summit') AND EventSponsorLogoUrl IS NOT NULL")
            ->filter($filter)
            ->sort('EventStartDate')
            ->limit($limit);

    }

    public function getSummitEvent()
    {
        return EventPage::get()->where("IsSummit = 1 AND EventStartDate > NOW()")->sort('EventStartDate')->first();
    }
}