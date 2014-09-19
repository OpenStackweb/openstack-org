<?php
	class IVotedPage extends Page {
		static $db = array(
		);
		static $has_one = array(
	     );
	}

	class IVotedPage_Controller extends Page_Controller {
		function init() {
			parent::init();
		}
	}