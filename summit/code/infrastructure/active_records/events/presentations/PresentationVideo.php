<?php

/**
 * Copyright 2015 OpenStack Foundation
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
class PresentationVideo extends PresentationMaterial
{
	/**
	 * @var array
     */
	private static $db = array (
        'YouTubeID' => 'Text',
        'DateUploaded' => 'SS_DateTime',
        'Highlighted' => 'Boolean',
        'Views' => 'Int'
    );

	/**
	 * @var array
     */
	private static $summary_fields = array (
    	'Name' => 'Title',
    	'Presentation.Summit.Title' => 'Summit',
    	'PresentationID' => 'Presentation ID',
    	'SpeakersCSV' => 'Speakers',
    	'Featured.Nice' => 'Featured?',
    	'Highlighted.Nice' => 'Highlighted?'
    );

	/**
	 * @var array
     */
	private static $better_buttons_actions = array (
    	'setasfeatured',
    	'unsetasfeatured'
    );

	/**
	 * @return FieldList
     */
	public function getCMSFields()
	{
		$f = parent::getCMSFields();		
		$f->addFieldToTab('Root.Main', new CheckboxField('Highlighted'),'Description');
		$f->addFieldToTab('Root.Main', new ReadonlyField('Views'),'Description');
		$f->addFieldToTab('Root.Main', new TextField('YouTubeID','YouTube ID'),'Description');
		$f->addFieldToTab('Root.Main', new ReadonlyField('DateUploaded'));
		$f->addFieldToTab('Root.Main', new ReadonlyField('PresentationTitle', 'Presentation title', $this->Presentation()->Title));
		return $f;
	}

	/**
	 * @return mixed
     */
	public function getBetterButtonsActions()
	{
		$f = parent::getBetterButtonsActions();
		if(!$this->Featured) {
			$f->push(BetterButtonCustomAction::create('setasfeatured', 'Set as the featured video')
	    				->setRedirectType(BetterButtonCustomAction::REFRESH));
		}
		else {
			$f->push(BetterButtonCustomAction::create('unsetasfeatured', 'Unmark as featured video')
	    				->setRedirectType(BetterButtonCustomAction::REFRESH));
		}

		return $f;

	}

	/**
	 * @param $data
	 * @param $form
	 * @throws ValidationException
	 * @throws null
     */
	public function setasfeatured($data, $form)
	{
		foreach(PresentationVideo::get()->filter('Featured', true) as $v) {
			$v->Featured = false;
			$v->write();
		}

		$this->Featured = true;
		$this->write();

		return 'This is now the featured video';
	}

	/**
	 * @param $data
	 * @param $form
	 * @throws ValidationException
	 * @throws null
     */
	public function unsetasfeatured($data, $form)
	{
		$this->Featured = false;
		$this->write();

		return 'This video is no longer featured';
	}

	/**
	 * @return mixed
     */
	public function getSpeakersCSV()
	{
		return $this->Presentation()->getSpeakersCSV();
	}

    protected function onBeforeWrite() {
        parent::onBeforeWrite();
        if(empty($this->DateUploaded) || $this->ID == 0 ){
            $this->DateUploaded = gmdate("Y-m-d H:i:s");
        }
    }
}