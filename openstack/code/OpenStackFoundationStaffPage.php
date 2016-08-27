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

class OpenStackFoundationStaffPage extends Page{

    static $can_be_root  = false;
    static $default_parent='OneColumn';
    static $allowed_children = "none";
    static $db = array(
        'ExtraFoundation' => 'HTMLText',
        'ExtraSupporting' => 'HTMLText',
        'ExtraFooter' => 'HTMLText',
    );

    static $defaults = array(
        "IncludeJquery" => 1,
        "IncludeShadowBox" => 1
    );

    function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->addFieldsToTab('Root.Main', new HtmlEditorField('ExtraFoundation', 'Extra Foundation Staff'));
        $fields->addFieldsToTab('Root.Main', new HtmlEditorField('ExtraSupporting', 'Extra Supporting Cast'));
        $fields->addFieldsToTab('Root.Main', new HtmlEditorField('ExtraFooter', 'Extra Footer'));
        return $fields;
    }
}

class OpenStackFoundationStaffPage_Controller extends Page_Controller{

    public function OpenStackFoundationStaffMembers(){
        //Group_Member
        $group = Group::get()->filter('Code','openstack-foundation-staff')->first();
        $res   = $group->getManyManyComponents("Members",'','Group_Members.SortOrder ASC');
        return $res;
    }

    public function SupportingCastMembers(){
        //Group_Member
  	    $group = Group::get()->filter('Code','supporting-cast')->first();
        $res= $group->getManyManyComponents("Members",'','Group_Members.SortOrder ASC');
        return $res;
    }

    public function init() {
        parent::init();
        Requirements::css(THEMES_DIR ."/openstack/css/bio.css");
    }
}