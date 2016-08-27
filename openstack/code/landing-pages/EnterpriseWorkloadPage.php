<?php
/**
 * Copyright 2014 Openstack Foundation
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
class EnterpriseWorkloadPage extends Page {
   static $db = array(
    );
} 
 
class EnterpriseWorkloadPage_Controller extends Page_Controller {

    function init()
    {
        parent::init();

        Requirements::CSS('themes/openstack/css/enterprise.css');

        Requirements::javascript('themes/openstack/javascript/filetracking.jquery.js');
        Requirements::javascript('themes/openstack/javascript/enterprise.js');   

        Requirements::customScript("jQuery(document).ready(function($) {

            $('body').filetracking();

            $(document).on('click', '.outbound-link', function(event){
                var href = $(this).attr('href');
                recordOutboundLink(this,'Outbound Links',href);
                event.preventDefault();
                event.stopPropagation()
                return false;
            });

        });");

    }

    public function getEnterpriseEvents($limit = 3)
    {
        return EventPage::get()->filter('EventCategory','Enterprise')->sort('EventStartDate')->limit($limit);
    }

    public function getSummitEvent()
    {
        return EventPage::get()->where("IsSummit = 1 AND EventStartDate > NOW()")->sort('EventStartDate')->first();
    }
}
 
?>