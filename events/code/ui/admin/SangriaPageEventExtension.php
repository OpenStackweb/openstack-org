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
 * Class SangriaPageEventExtension
 */
final class SangriaPageEventExtension extends Extension {

	private $repository;
    private $event_repository;

	public function __construct(){
		parent::__construct();
		$this->repository = new SapphireEventRegistrationRequestRepository;
        $this->event_repository = new SapphireEventRepository;
	}

	public function onBeforeInit(){
		Config::inst()->update(get_class($this), 'allowed_actions', array('ViewEventDetails','ViewPostedEvents','ViewOpenstackDaysEvents','FeaturedEventForm','saveFeaturedEvent'));
		Config::inst()->update(get_class($this->owner), 'allowed_actions', array('ViewEventDetails','ViewPostedEvents','ViewOpenstackDaysEvents','FeaturedEventForm','saveFeaturedEvent'));
	}

	public function EventRegistrationRequestForm() {
		$this->commonScripts();
		Requirements::css('events/css/event.registration.form.css');
		Requirements::javascript("events/js/event.registration.form.js");
		$data = Session::get("FormInfo.Form_EventRegistrationRequestForm.data");
		$form = new EventRegistrationRequestForm($this->owner, 'EventRegistrationRequestForm',false);
		// we should also load the data stored in the session. if failed
		if(is_array($data)) {
			$form->loadDataFrom($data);
		}
		// Optional spam protection
		if(class_exists('SpamProtectorManager')) {
			SpamProtectorManager::update_form($form);
		}
		return $form;
	}

    public function EventForm() {
        $this->commonScripts();
        Requirements::css('events/css/event.registration.form.css');
        Requirements::javascript("events/js/event.registration.form.js");
        $data = Session::get("FormInfo.Form_EventForm.data");
        $form = new EventForm($this->owner, 'EventForm',false);
        // we should also load the data stored in the session. if failed
        if(is_array($data)) {
            $form->loadDataFrom($data);
        }
        // Optional spam protection
        if(class_exists('SpamProtectorManager')) {
            SpamProtectorManager::update_form($form);
        }
        return $form;
    }

    public function FeaturedEventForm() {
        $fields = new FieldList;
        //main info
        $events = EventPage::get('EventPage',"EventCategory = 'OpenStack Days'");
        $options = array();
        foreach($events as $event) {
            $options[$event->ID] = $event->Title;
        }

        $fields->push(new DropdownField('EventID','Event',$options));
        $ImageField = new CustomUploadField('Picture', 'Image (Max size 2Mb - Suggested size 300x250px)');
        $ImageField->setCanAttachExisting(false);
        $ImageField->setAllowedMaxFileNumber(1);
        $ImageField->setAllowedFileCategories('image');
        $ImageField->setTemplateFileButtons('CustomUploadField_FrontEndFIleButtons');
        $ImageField->setFolderName('news-images');
        $ImageField->setRecordClass('BetterImage');
        $ImageField->getUpload()->setReplaceFile(false);
        $ImageField->setOverwriteWarning(false);
        $sizeMB = 2; // 2 MB
        $size = $sizeMB * 1024 * 1024; // 2 MB in bytes
        $ImageField->getValidator()->setAllowedMaxFileSize($size);
        $ImageField->setCanPreviewFolder(false); // Don't show target filesystem folder on upload field
        $fields->push($ImageField);

        // Create action
        $actions = new FieldList();
        $actions->push(new FormAction('saveFeaturedEvent', 'Save'));

        return new Form($this->owner, 'FeaturedEventForm', $fields, $actions);
    }

    public function saveFeaturedEvent($data, $form) {
        $submission = new FeaturedEvent();
        $form->saveInto($submission);
        $submission->write();
        return $this->owner->redirectBack();
    }

    public function removeFeaturedEvent() {
        $featured_event_id = (int)$this->request->param('EVENT_ID');
        FeaturedEvent::delete_by_id('FeaturedEvent',$featured_event_id);
        return $this->owner->redirectBack();
    }

	public function onAfterInit(){

	}

	private function commonScripts(){
		Requirements::css("themes/openstack/css/chosen.css", "screen,projection");
		Requirements::css(THIRDPARTY_DIR . '/jquery-ui-themes/smoothness/jquery-ui.css');
		Requirements::javascript(THIRDPARTY_DIR . '/jquery-ui/jquery-ui.js');
		Requirements::css("events/css/sangria.page.view.event.details.css");
		Requirements::javascript("themes/openstack/javascript/chosen.jquery.min.js");
		Requirements::javascript(Director::protocol()."ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");
		Requirements::javascript(Director::protocol()."ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/additional-methods.min.js");
		Requirements::javascript("themes/openstack/javascript/jquery.ui.datepicker.validation.package-1.0.1/jquery.ui.datepicker.validation.js");
		Requirements::javascript("themes/openstack/javascript/jquery.validate.custom.methods.js");
		Requirements::javascript('marketplace/code/ui/admin/js/utils.js');
		Requirements::javascript('themes/openstack/javascript/jquery.cleanform.js');
	}

	public function ViewEventDetails(){
		$this->commonScripts();
		Requirements::javascript('events/js/admin/sangria.page.event.extension.js');
		return $this->owner->getViewer('ViewEventDetails')->process($this->owner);
	}

    public function ViewPostedEvents(){
        $this->commonScripts();
        Requirements::javascript('events/js/admin/sangria.page.event.extension.js');
        return $this->owner->getViewer('ViewPostedEvents')->process($this->owner);
    }

    public function ViewOpenstackDaysEvents(){
        $this->commonScripts();
        Requirements::javascript('events/js/admin/sangria.page.event.extension.js');
        return $this->owner->getViewer('ViewOpenstackDaysEvents')->process($this->owner);
    }

	public function getQuickActionsExtensions(&$html){
		$view = new SSViewer('SangriaPage_EventLinks');
		$html .= $view->process($this->owner);
	}

	public function getEventRegistrationRequest(){
		list($list,$size) = $this->repository->getAllNotPostedAndNotRejected(0,1000);
		return new ArrayList($list);
	}

    public function getPostedEvents(){
        $list = $this->event_repository->getAllPosted(0,1000);
        return new ArrayList($list);
    }

    public function getPostedEventsCount(){
        $count = $this->event_repository->countAllPosted();
        return $count;
    }

    public function getFeaturedEvents(){
        return FeaturedEvent::get('FeaturedEvent');
    }
} 
