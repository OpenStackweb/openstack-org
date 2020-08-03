<?php
/**
 * Copyright 2020 OpenStack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
class GeneralEventsLandingPage extends Page
{

}

class GeneralEventsLandingPage_Controller extends Page_Controller
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

        Requirements::css('summit/css/general-events-landing-page.css');

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
