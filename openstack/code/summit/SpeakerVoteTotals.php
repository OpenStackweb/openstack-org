<?php
	class SpeakerVoteTotals extends Page {
		static $db = array(
		);
		static $has_one = array(
	     );
	}

	class SpeakerVoteTotals_Controller extends Page_Controller {

		function init() {
			parent::init();
		}
		
		function SpeakerSubmissions() {
			$subs = SpeakerSubmission::get()->sort("VoteTotal");
			return $subs;
		}


		function SpeakerVotes() {
			$votes = SpeakerVote::get();
			return $votes;
		}


		public function SiteAdmin() { 
			if(Permission::check('ADMIN')) return true; 
		}
				
	}