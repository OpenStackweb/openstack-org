<?php


class GeneralSummitLandingPage extends Page
{

}


class GeneralSummitLandingPage_Controller extends Page_Controller
{

    public function init()
    {
        parent::init();
        SweetAlert2Dependencies::renderRequirements();
        Requirements::javascript("summit/javascript/summit.js");
        // Summit pages are so different visually we don't pull in the main css file
        Requirements::block("themes/openstack/css/combined.css");
        Requirements::css("summit/css/combined.css");
        Requirements::css("themes/openstack/css/static.combined.css");
        FontAwesomeDependencies::renderRequirements();
        Requirements::css('node_modules/@fortawesome/fontawesome-pro/css/all.css');

        Requirements::css('summit/css/general-summit-landing-page.css');

    }

    public function getPageTitle()
    {
        return SummitPage::PageCustomTitle;
    }

    public function MetaTags()
    {
        $tags = parent::MetaTags();
        return $tags;
    }

    function getCurrentSummit() {
        return Summit::ActiveSummit();
    }

    function getCurrentSummitPage() {
        $currentSummit = Summit::ActiveSummit();
        if(is_null($currentSummit)) return null;
        $summitPage = SummitPage::get()->filter('SummitID', $currentSummit->ID)->first();
        if(is_null($summitPage)) return null;

        while(!is_null($summitPage) && $summitPage->Parent()->exists() && $summitPage->Parent()->is_a('SummitPage')) {
            $summitPage = $summitPage->Parent();
        }

        return $summitPage;
    }

    function getCurrentSummitPageController() {
        $summitPage = $this->getCurrentSummitPage();
        if(is_null($summitPage)) return null;
        return ModelAsController::controller_for($summitPage);
    }

    public function isMultiRegister() {
        $summitPage = $this->getCurrentSummitPage();
        if(!$summitPage) return false;
        if ($summitPage->Summit()->ID == 27) return true;
        return false;

    }

    function getMenuItems() {
        $summitPage = $this->getCurrentSummitPage();

        //$menu = $this->getCurrentSummitPageController()->Menu(3);
        //$menu->unshift($summitPage);

        $menu = new ArrayList([$summitPage]);
        return $menu;
    }

}
