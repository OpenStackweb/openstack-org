<?php

/**
 * Copyright 2017 Openstack Foundation
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
class CommunityPageBis extends Page
{
    static $db = array(
        'TopBanner' => 'HTMLText'
    );

    static $has_one = array();

    static $has_many = array(
        'CommunityManagers' => 'Member',
        'Embassadors'       => 'Member',
    );

    function getCMSFields()
    {
        $fields = parent::getCMSFields();

        return $fields;
    }

}

class CommunityPageBis_Controller extends Page_Controller
{

    private static $allowed_actions = array( );

    static $url_handlers = array( );

    function init()
    {
        parent::init();

        Requirements::css('themes/openstack/css/community-bis.css');
        Requirements::javascript('themes/openstack/javascript/community-bis.js');
    }

    function getProjectGroups() {
        $groups = OpenStackComponent::$categories;
        $list = new ArrayList();
        foreach ($groups as $key => $group) {
            $list->push(new ArrayData([
                'Name' => $group,
                'Key'  => $key
            ]));
        }

        return $list;
    }

    function getComponentsByGroup($group) {
        return OpenStackComponent::get()->filter('Use', $group);
    }

}