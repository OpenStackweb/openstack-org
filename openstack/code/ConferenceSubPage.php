<?php
/**
 * Defines the ConferenceSubPage page type
 */
class ConferenceSubPage extends Page {
   static $db = array(
      'HideSideBar' => 'Boolean'
	);
   static $has_one = array(
   );
   
 	function getCMSFields() {
    	$fields = parent::getCMSFields();

      $fields->addFieldToTab('Root.Metadata', new CheckboxField ('HideSideBar','Hide The Sidebar on this page.'));
    	
    	return $fields;
 	}   
}
 
class ConferenceSubPage_Controller extends Page_Controller {
	function init() {
	    parent::init();
	}

  function TrackingLink() {

    // Get the tracking code from the session if one is set.
    $source = Session::get('TrackingLinkSource');

    // Now look to see if a tracking code was passed in via a URL param.
    // This will override what's in the session if need be
    $getVars = $this->request->getVars();

    if(isset($getVars['source'])) {
      $source = Convert::raw2sql($getVars['source']);
      // Save the source id from the URL param into the session
      Session::set('TrackingLinkSource', $source);
    }

    return $source;   
  }

  function TrackingLinkScript() {

    $trackingLink = $this->TrackingLink();

    if($trackingLink) {

      $script = '

      <script type="text/javascript">

      $(function() {
         $("a.tracking-link").attr("href", function(i, h) {
           return h + ("'.$trackingLink.'");
         });
      });

      </script>


      ';

      return $script;

    }

  }

}