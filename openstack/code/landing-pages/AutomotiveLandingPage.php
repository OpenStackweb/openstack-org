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

	    Requirements::javascript('themes/openstack/javascript/filetracking.jquery.js');

	    Requirements::customScript("jQuery(document).ready(function($) {


            $('body').filetracking();

            $('.outbound-link').live('click',function(event){
                var href = $(this).attr('href');
                recordOutboundLink(this,'Outbound Links',href);
                event.preventDefault();
                event.stopPropagation()
                return false;
            });
        });");

    } 
}
 
?>