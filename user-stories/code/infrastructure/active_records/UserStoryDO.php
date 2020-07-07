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

class UserStoryDO extends DataObject implements IUserStory
{

	static $db = array(
		'Name'             => 'Text',
		'Description'      => 'HTMLText',
		'ShortDescription' => 'HTMLText',
        'Link'             => 'Text',
        'Active'           => 'Boolean(1)',
        'ShowAtHomePage'   => 'Boolean',
	);

	static $has_one = array(
		'Industry'      => 'UserStoriesIndustry',
        'Organization'  => 'Org',
        'Location'      => 'Continent',
        'Image'         => 'CloudImage'
	);

    private static $many_many = array
    (
        'Tags'      => 'Tag',
    );

	static $singular_name = 'User Story';
	static $plural_name = 'User Stories';


    public function getIdentifier() {
        return $this->ID;
    }

    public function getName() {
        if ($this->Organization()->Exists()) return $this->Organization()->Name;
        return $this->getField('Name');
    }

}