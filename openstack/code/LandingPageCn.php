<?php

class LandingPageCn extends Page {
    function getCMSFields() {
        $fields = parent::getCMSFields();
        // remove unneeded fields
        $fields->removeFieldFromTab("Root.Main","Content");
        return $fields;
    }

    // LandingPageCn can't contain children
    static $allowed_children = "none";

    static $defaults = array(
		'ShowInMenus' => false
	);

}

class LandingPageCn_Controller extends Page_Controller {
    public function init() {
        parent::init();
        Requirements::customScript("Shadowbox.init();");
    }
}