<?php
/**
 * Copyright 2018 OpenStack Foundation
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

class StaticSummitAboutPage extends SummitPage {

}

class StaticSummitAboutPage_Controller extends SummitPage_Controller {

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
}