<?php

class StaticSummitAboutPage extends Page {

}


class StaticSummitAboutPage_Controller extends Page_Controller {

    public function init()
    {
        parent::init();

        Requirements::block('summit/css/combined.css');
        Requirements::css('node_modules/@fortawesome/fontawesome-pro/css/all.css');
        Requirements::css('themes/openstack/static/css/combined.css');
        Requirements::css('summit/css/static-summit-about-page.css');
		Requirements::javascript('summit/javascript/in-view.min.js');
		Requirements::javascript('summit/javascript/static-summit-about-page.js');

    }

    public function getSummitAboutPageLink() {
        return $this->Link();
    }

    public function getAboutPageNavClass(){
        return 'current';
    }
}