<?php
	class DirectAfterLoginPage extends Page {
		static $db = array(
		);
		static $has_one = array(
	     );
		static $defaults = array(
       		'ShowInMenus' => false
    	);

	}

	class DirectAfterLoginPage_Controller extends Page_Controller {
		function init() {
			parent::init();
		}		

		function FoundationMember() {
			$currentMember = Member::currentUser();
			// see if the member is inu the foundation group
			if ($currentMember && $currentMember->inGroup(5)) return TRUE;
		}

		function CallForSpeakersLink() {
			// Find the call for speakers page for the current summit
			$SummitPage = Page::get()->filter('URLSegment','summit')->first();
			$URL = $SummitPage->Link() . 'call-for-speakers/';
			return $URL;
		}


	}