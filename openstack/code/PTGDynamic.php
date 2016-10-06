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
 * Defines the PTGDynamic page type
 */
class PTGDynamic extends Page {

    private static $db = array(
        'Header'             => 'HTMLText',
        'Summary'            => 'HTMLText',
        'WhyTheChange'       => 'HTMLText',
        'HotelAndTravel'     => 'HTMLText',
        'HotelLink'          => 'VarChar(255)',
        'WhoShouldAttend'    => 'HTMLText',
        'WhoShouldNotAttend' => 'HTMLText',
        'Benefits'           => 'HTMLText',
        'Sponsor'            => 'HTMLText',
        'SponsorSteps'       => 'HTMLText',
        'TravelSupport'      => 'HTMLText',
        'TravelSupportApply' => 'HTMLText',
        'RegisterToAttend'   => 'HTMLText',
        'PTGSchedule'        => 'HTMLText',
        'CodeOfConduct'      => 'HTMLText',
        'FindOutMore'        => 'HTMLText',
    );

    private static $has_one = array(
        'Graph' => 'BetterImage',
        'ScheduleImage' => 'BetterImage',
    );

    function getCMSFields(){
        $fields = parent::getCMSFields();
        $fields->removeByName('Content');

        $fields->addFieldToTab('Root.Main', $header = new HtmlEditorField('Header','Header'));
        $header->setRows(5);
        $fields->addFieldToTab('Root.Main', $summary = new HtmlEditorField('Summary','Summary'));
        $summary->setRows(5);
        $fields->addFieldToTab('Root.Main', $whychange = new HtmlEditorField('WhyTheChange','Why The Change'));
        $whychange->setRows(5);
        $fields->addFieldToTab(
            'Root.Main',
            $graph = new UploadField('Graph', 'Graph')
        );
        $fields->addFieldToTab('Root.Main', $hotel = new HtmlEditorField('HotelAndTravel','Hotel & Travel'));
        $hotel->setRows(5);
        $fields->addFieldToTab('Root.Main', new TextField('HotelLink','Hotel Link'));
        $fields->addFieldToTab('Root.Main', $attend = new HtmlEditorField('WhoShouldAttend','Who Should Attend'));
        $attend->setRows(5);
        $fields->addFieldToTab('Root.Main', $notattend = new HtmlEditorField('WhoShouldNotAttend','Who Should Not Attend'));
        $notattend->setRows(5);
        $fields->addFieldToTab('Root.Main', $benefit = new HtmlEditorField('Benefits','How can this benefit?'));
        $benefit->setRows(5);
        $fields->addFieldToTab('Root.Main', $sponsor = new HtmlEditorField('Sponsor','What are PTG Events & Why Sponsor?'));
        $sponsor->setRows(5);
        $fields->addFieldToTab('Root.Main', $sponsor_steps = new HtmlEditorField('SponsorSteps','Steps to Sponsoring the PTG event'));
        $sponsor_steps->setRows(5);
        $fields->addFieldToTab('Root.Main', $travel_sup = new HtmlEditorField('TravelSupport','Travel Support Program'));
        $travel_sup->setRows(5);
        $fields->addFieldToTab('Root.Main', $travel_sup_app = new HtmlEditorField('TravelSupportApply','Apply for Travel Support'));
        $travel_sup_app->setRows(5);
        $fields->addFieldToTab('Root.Main', $register = new HtmlEditorField('RegisterToAttend','Register to Attend'));
        $register->setRows(5);
        $fields->addFieldToTab('Root.Main', $schedule = new HtmlEditorField('PTGSchedule','PTG Schedule'));
        $schedule->setRows(5);
        $fields->addFieldToTab(
            'Root.Main',
            $schedule_image = new UploadField('ScheduleImage', 'Schedule')
        );
        $fields->addFieldToTab('Root.Main', $coc = new HtmlEditorField('CodeOfConduct','Code of Conduct'));
        $coc->setRows(5);
        $fields->addFieldToTab('Root.Main', $findout = new HtmlEditorField('FindOutMore','Find Out More'));
        $findout->setRows(5);

        return $fields;

    }

}

class PTGDynamic_Controller extends Page_Controller {
	function init() {
		parent::init();
        Requirements::css('themes/openstack/css/ptg.css');
	}
}