<?php

class AutomotiveLandingPage extends Page {
   static $db = array(
	);
}
 
class AutomotiveLandingPage_Controller extends Page_Controller {

    function init()
    {
        parent::init();
        Requirements::clear();
    } 
}
 
?>