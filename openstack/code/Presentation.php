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
class Presentation extends DataObject
{

	static $db = array(
		'Name' => 'HTMLText',
		'DisplayOnSite' => 'Boolean',
		'Featured' => 'Boolean',
		'City' => 'Varchar(255)',
		'Country' => 'Varchar(255)',
		'Description' => 'HTMLText',
		'YouTubeID' => 'Varchar(255)',
		'URLSegment' => 'Text',
		'StartTime' => 'Varchar(255)',
		'EndTime' => 'Varchar(255)',
		'Location' => 'Text',
		'Type' => 'Text',
		'Day' => 'Int',
		'Speakers' => 'Text',
		'SlidesLink' => 'Varchar(255)',
		'event_key' => 'Varchar(255)',
		'IsKeynote' => 'Boolean'
	);

	Static $defaults = array(
		'DisplayOnSite' => TRUE,
		'Country' => 'United States'
	);

	static $has_one = array(
		'PresentationCategoryPage' => 'PresentationCategoryPage',
		'Summit' => 'Summit',
		'Member' => 'Member',
	);

	static $has_many = array(
		'Presentations' => 'File'
	);

	static $singular_name = 'Presentation';
	static $plural_name = 'Presentations';

	static $summary_fields = array(
		'Name' => 'Presentation Name'
	);

	function getCMSFields()
	{
		$fields = new FieldList (
			new TextField('Name', 'Name of Presentation'),
			new TextField('Speakers', 'Speakers'),
			new DropdownField('Day', 'Day', array('1' => '1', '2' => '2', '3' => '3', '4' => '4')),
			new TextField('URLSegment', 'URL Segment'),
			new LiteralField('Break', '<p>(Automatically filled in on first save.)</p>'),
			new LiteralField('Break', '<hr/>'),
			new TextField('YouTubeID', 'YouTube Vidoe ID'),
			new TextField('SlidesLink', 'Link To Slides (if available)'),
			new TextField('StartTime', 'Video Start Time'),
			new TextField('EndTime', 'Video End Time'),
			new HTMLEditorField('Description', 'Description'),
			new CheckboxField('IsKeynote','Keynote Presenation')
		);
		return $fields;
	}


	function FormattedStartTime()
	{
		$start_time = trim(str_replace("p.m.", "", trim($this->StartTime)));
		$date = DateTime::createFromFormat('d/m/y H:i', $start_time);
		if (!$date)
			$date = DateTime::createFromFormat('Y-m-d H:i:s', $start_time);
		if ($date)
			return $date->format('l h:i a');
		return 'N/A';
	}

	function PresentationDay()
	{
		$start_time = trim(str_replace("p.m.", "", trim($this->StartTime)));
		$date = DateTime::createFromFormat('d/m/y H:i', $start_time);
		if (!$date)
			$date = DateTime::createFromFormat('Y-m-d H:i:s', $start_time);
		if ($date)
			return $date->format('M d');
		return 'N/A';
	}

	private function generateURLSegment($title){
		$filter = URLSegmentFilter::create();
		$t = $filter->filter($title);

		// Fallback to generic page name if path is empty (= no valid, convertable characters)
		if(!$t || $t == '-' || $t == '-1') $t = "page-$this->ID";

		return $t;
	}	

	function onBeforeWrite()
	{
		parent::onBeforeWrite();


		// If there is no URLSegment set, generate one from Title
		if ((!$this->URLSegment || $this->URLSegment == 'new-presentation') && $this->Title != 'New Presentation') {
			$this->URLSegment = $this->generateURLSegment($this->Title);
		} else if ($this->isChanged('URLSegment')) {
			// Make sure the URLSegment is valid for use in a URL
			$segment = preg_replace('/[^A-Za-z0-9]+/', '-', $this->URLSegment);
			$segment = preg_replace('/-+/', '-', $segment);

			// If after sanitising there is no URLSegment, give it a reasonable default
			if (!$segment) {
				$segment = "presentation-" . $this->ID;
			}
			$this->URLSegment = $segment;
		}

		// Ensure that this object has a non-conflicting URLSegment value by appending number if needed
		$count = 2;
		while ($this->LookForExistingURLSegment($this->URLSegment)) {
			$this->URLSegment = preg_replace('/-[0-9]+$/', null, $this->URLSegment) . '-' . $count;
			$count++;
		}

	}

//Test whether the URLSegment exists already on another Video
	function LookForExistingURLSegment($URLSegment)
	{
		return (DataObject::get_one('Presentation', "URLSegment = '" . $URLSegment . "' AND ID != " . $this->ID));
	}

// Pull video thumbnail from YouTube API
	function ThumbnailURL()
	{
		if ($this->YouTubeID) {
			return "http://i.ytimg.com/vi/" . $this->YouTubeID . "/default.jpg";
		}
	}

//Generate the link for this product
	function ShowLink()
	{
		$ParentPage = $this->PresentationCategoryPage();

		if ($ParentPage) {
			return $ParentPage->Link() . "presentation/" . $this->URLSegment;
		}
	}

// See if the presentation slides can be embedded
	function EmbedSlides()
	{
		// Slides can be emdedded if they are hosted on crocodoc. Otherwise, there's only a download button displayed by the template
		if (strpos($this->SlidesLink, 'crocodoc.com') !== false) {
			return true;
		}
	}

	function PopulateFromSchedEvent($SchedEventID)
	{

		$SchedEvent = SchedEvent::get()->byID($SchedEventID);

		$this->Name = $SchedEvent->eventtitle;
		$this->DisplayOnSite = TRUE;
		$this->Description = $SchedEvent->description;
		$this->StartTime = $SchedEvent->event_start;
		$this->EndTime = $SchedEvent->event_end;
		$this->Type = $SchedEvent->event_type;
		$this->Speakers = $SchedEvent->speakers;
		$this->event_key = $SchedEvent->event_key;
		$this->write();

	}

	function AddYouTubeID($YouTubeID)
	{
		$this->YouTubeID = $YouTubeID;
		$this->write();
	}

}