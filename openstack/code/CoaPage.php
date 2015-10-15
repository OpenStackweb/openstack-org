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
	class CoaPage extends Page {
		static $db = array(
		);
		static $has_one = array(
	     );
	}

	class CoaPage_Controller extends Page_Controller {

		function init() {
			parent::init();

            Requirements::customScript(<<<JS
  !function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?"http":"https";if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://s3.amazonaws.com/subscription-cdn/0.2/widget.min.js";fjs.parentNode.insertBefore(js,fjs);}}(document, "script", "sendgrid-subscription-widget-js");
JS
            );
		}		
	}