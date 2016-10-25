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
	class UserStoriesStatic extends Page {
		static $db = array(
		);
		static $has_one = array(
	     );
	}

	class UserStoriesStatic_Controller extends Page_Controller {
		function init() {
			parent::init();
		}

        public function getEnterpriseEvents($limit = 3)
        {
            $next_summit = $this->getSummitEvent();
            $filter = array("EventEndDate:GreaterThan" => date('Y-m-d H:i:s'), "ID:not" => $next_summit->ID);
            return EventPage::get()
                ->where("EventCategory IN('Enterprise','Summit')")
                ->filter($filter)
                ->sort('EventStartDate')
                ->limit($limit);
        }

        public function getEnterpriseFeaturedEvents($limit = 3)
        {
            $next_summit = $this->getSummitEvent();
            $filter = array("EventEndDate:GreaterThan" => date('Y-m-d H:i:s'), "ID:not" => $next_summit->ID);
            return EventPage::get()
                ->where("EventCategory IN('Enterprise','Summit') AND EventSponsorLogoUrl IS NOT NULL")
                ->filter($filter)
                ->sort('EventStartDate')
                ->limit($limit);

        }

        public function getSummitEvent()
        {
            return EventPage::get()->where("IsSummit = 1 AND EventStartDate > NOW()")->sort('EventStartDate')->first();
        }
	}