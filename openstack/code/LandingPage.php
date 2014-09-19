<?php
 
class LandingPage  extends Page {
    function getCMSFields() {
        $fields = parent::getCMSFields();
        // remove unneeded fields
        $fields->removeFieldFromTab("Root.Main","Content");
        return $fields;
    }

    // LandingPage can't contain children
    static $allowed_children = "none";

    static $defaults = array(
		'ShowInMenus' => false
	);

}

class LandingPage_Controller extends Page_Controller {
    public function init() {
        parent::init();
        Requirements::customScript("Shadowbox.init();");
    }
}