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

/**
 * Class SangriaPageMemberBadgesExtension
 */
final class SangriaPageMemberBadgesExtension extends Extension {

	public function onBeforeInit(){
		Config::inst()->update(get_class($this), 'allowed_actions', array(
			'IngestMemberBadges',
			'IngestMemberBadgesForm',
		));

		Config::inst()->update(get_class($this->owner), 'allowed_actions', array(
			'IngestMemberBadges',
			'IngestMemberBadgesForm',
		));
	}

	function IngestMemberBadgesForm() {
		return new IngestMemberBadgesForm($this->owner, 'IngestMemberBadgesForm');
	}

} 