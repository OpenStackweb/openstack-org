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
 * Defines the JobsHolder page type
 */
class PresentationCategoryPage extends Page
{
    private static $db = array(
        'StillUploading' => 'Boolean',
        'FeaturedVideoLabel' => 'Text',
        'FeaturedVideoDescription' => 'Text'
    );

    private static $has_one = array();

    private static $has_many = array(
        'Presentations'  => 'VideoPresentation',
        'FeaturedVideos' => 'FeaturedVideo',
    );

    private static $allowed_children = array('PresentationCategoryPage');

    /** static $icon = "icon/path"; */

    function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $presentationsTable = new GridField('Presentations', 'Presentations', $this->Presentations(),
            GridFieldConfig_RecordEditor::create(10));

        $fields->addFieldToTab('Root.Presentations', $presentationsTable);

        //Featured Videos Descriptions
        $FeaturedVideoLabelField = new TextField('FeaturedVideoLabel', 'Label for featured vdieos');
        $FeaturedVideoDescriptionField = new TextField('FeaturedVideoDescription', 'Label for featured vdieos');

        $fields->addFieldToTab("Root.Main", $FeaturedVideoLabelField, 'Content');
        $fields->addFieldToTab("Root.Main", $FeaturedVideoDescriptionField, 'Content');

        // Summit Videos
        $VideosUploadingField = new OptionSetField('StillUploading', 'Are videos still being uploaded?', array(
            '1' => 'Yes - A message will be displayed.',
            '0' => 'No'
        ));

        $fields->addFieldToTab("Root.Main", $VideosUploadingField, 'Content');

        $featuredVideos = new GridField('FeaturedVideos', 'FeaturedVideos', $this->FeaturedVideos(),
            GridFieldConfig_RecordEditor::create(10));
        $fields->addFieldToTab('Root.FeaturedVideos', $featuredVideos);

        $presentations = new GridField('Presentations', 'Presentations', $this->Presentations(),
            GridFieldConfig_RecordEditor::create(10));
        $fields->addFieldToTab('Root.Presentations', $presentations);


        return $fields;
    }

}

class PresentationCategoryPage_Controller extends Page_Controller
{


    static $allowed_actions = array(
        'presentation',
        'featured',
        'updateURLS' => 'admin'
    );

    public function Presentations()
    {
        $sessions = dataobject::get('VideoPresentation',
            '(`YouTubeID` IS NOT NULL OR `SlidesLink` IS NOT NULL) AND PresentationCategoryPageID = ' . $this->ID,
            'StartTime DESC');

        return $sessions;
    }

    function init()
    {

        parent::init();
        if (isset($_GET['day'])) {
            Session::set('Day', $_GET['day']);
        } else {
            Session::set('Day', 1);
        }

        if ($this->getRequest()->getVar("OtherID") != "presentation") {
            Session::set('Autoplay', true);
        }
    }

    //Show the Presentation detail page using the PresentationCategoryPage_presentation.ss template
    function presentation()
    {
        if ($Presentation = $this->getPresentationByURLSegment()) {
            $Data = array(
                'Presentation' => $Presentation
            );

            $this->Title = $Presentation->Name;
            $this->Autoplay = Session::get('Autoplay');

            // Clear autoplay so it only happens when you come directly from videos index
            Session::set('Autoplay', false);

            //return our $Data to use on the page
            return $this->Customise($Data);
        } else {
            //Presentation not found
            return $this->httpError(404, 'Sorry that presentation could not be found');
        }
    }

    //Show the Presentation detail page using the PresentationCategoryPage_presentation.ss template
    function featured()
    {
        if ($Presentation = $this->getPresentationByURLSegment(true)) {
            $Data = array(
                'Presentation' => $Presentation
            );

            $this->Title = $Presentation->Name;
            $this->Autoplay = Session::get('Autoplay');

            // Clear autoplay so it only happens when you come directly from videos index
            Session::set('Autoplay', false);

            //return our $Data to use on the page
            return $this->Customise($Data);
        } else {
            //Presentation not found
            return $this->httpError(404, 'Sorry that presentation could not be found');
        }
    }

    function Keynotes()
    {
        return $this->Presentations()->filter('IsKeynote', true);
    }

    function PresentationDayID($PresentationDay)
    {
        return trim($PresentationDay, ' ');
    }

    function LatestPresentation()
    {
        if ($this->Presentations()) {
            return $this->Presentations()->first();
        }
    }


    // Check to see if the page is being accessed in Chinese
    // We use this in the templates to tell Chinese visitors how to obtain the videos on a non-youtube source
    function ChineseLanguage()
    {
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        if ($lang == "zh") {
            return true;
        } else {
            return false;
        }
    }


    //Get the current Presentation from the URL, if any
    public function getPresentationByURLSegment($featured = false)
    {

        $Params = $this->getURLParams();
        $Segment = convert::raw2sql($Params['ID']);

        if ($featured == false && $Params['ID'] && $Presentation = DataObject::get_one('VideoPresentation',
                "`URLSegment` = '" . $Segment . "' AND `PresentationCategoryPageID` = " . $this->ID)
        ) {
            return $Presentation;
        } elseif ($featured == true && $Params['ID'] && $FeaturedVideo = DataObject::get_one('FeaturedVideo',
                "`URLSegment` = '" . $Segment . "'")
        ) {
            return $FeaturedVideo;
        }
    }


    function currentDay()
    {
        $day = Session::get('Day');
        Session::clear('Day');

        // Casting the value as int prevents possible XSS attack
        return (int)$day;
    }

    function updateURLS()
    {
        $presentations = dataobject::get('VideoPresentation', 'PresentationCategoryPageID = ' . $this->ID,
            'StartTime ASC');
        foreach ($presentations as $presentation) {
            if ($presentation->URLSegment == null) {
                $presentation->write();
            }
        }
        echo "Presentation URLS updated.";
    }

    public function GroupedPresentations()
    {

        return GroupedList::create($this->Presentations()->sort('StartTime'));
    }

    protected function CustomScripts()
    {
        Requirements::javascript(  "themes/openstack/javascript/navigation.js");
        Requirements::javascript("themes/openstack/javascript/filetracking.jquery.js");
        Requirements::javascript("themes/openstack/javascript/videos.js");
    }

    public function PresentationFileName($media_link)
    {
        $res = parse_url($media_link);

        return basename($res['path']);
    }

}
