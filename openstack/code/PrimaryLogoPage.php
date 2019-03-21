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
	class PrimaryLogoPage extends Page {
		static $db = array(
		);
		static $has_one = array(
	     );

		function getCMSFields() {
			$fields = parent::getCMSFields();
			
			return $fields;
		}   
			     				
		
	}

	class PrimaryLogoPage_Controller extends Page_Controller {
			
		function init() {
			// Populate the backURL session variable so login form sends us back to this page
			Session::set('BackURL', $this->Link());
			parent::init();

            Requirements::css('themes/openstack/css/logo-page.css');

        }
		
		function BrandingMenu() {
			return TRUE;
		}
		
				
	}