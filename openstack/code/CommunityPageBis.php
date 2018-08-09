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

    static $many_many = array(
        'CommunityManagers' => 'Member',
        'Ambassadors'       => 'Member',
    );

    static $many_many_extraFields = array(
        'CommunityManagers' => array(
            'Order' => "Int",
        ),
        'Ambassadors' => array(
            'Order' => "Int",
        ),
    );

    function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('Content');

        $config = new GridFieldConfig_RelationEditor(5);
        $config->addComponent($sort = new GridFieldSortableRows('Order'));
        $config->removeComponentsByType('GridFieldAddNewButton');
        $managers = new GridField('CommunityManagers', 'Community Managers', $this->CommunityManagers(), $config);
        $fields->addFieldToTab('Root.Main', $managers);

        $config = new GridFieldConfig_RelationEditor(12);
        $config->addComponent($sort = new GridFieldSortableRows('Order'));
        $config->removeComponentsByType('GridFieldAddNewButton');
        $ambassadors = new GridField('Ambassadors', 'Ambassadors', $this->Ambassadors(), $config);
        $fields->addFieldToTab('Root.Main', $ambassadors);

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

    function getCategoriesWithComponents() {
        $categories     = OpenStackComponentCategory::get();
        $parentCatIds   = [];

        foreach($categories as $category) {
            if ($category->OpenStackComponents()->count() > 0) {
                $parentCatIds[] = $category->ID;
            }
        }

        if (count($parentCatIds)) {
            $ids = implode(',', $parentCatIds);
            return $categories->where("ID IN ({$ids})");
        }

        return [];
    }

    function MascotImage($component_slug) {
        $slugWithoutSpaces = str_replace(" ", "_", $component_slug);
        return '/software/images/mascots/' . $slugWithoutSpaces . '.png';
    }

    function CountryName($code) {
        return CountryCodes::countryCode2name($code);
    }
}