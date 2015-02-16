<?php

/* 
   Used to set a clean, stripped-down template for legal and terms pages
   These pages contain no nav and just a back button
*/

class SummitSimplePage extends Page {

}


class SummitSimplePage_Controller extends Page_Controller {
	private static $allowed_actions = array (
		'back'
	);    
    
    public function back() {
        return Controller::redirect('/summit/vancouver-2015/details-and-updates/');
    }

}