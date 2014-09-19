<?php
/**
 * Created by JetBrains PhpStorm.
 * User: smarcet
 * Date: 8/15/13
 * Time: 5:39 PM
 * To change this template use File | Settings | File Templates.
 */

class OpenStackFoundationStaffPage extends Page{

    static $can_be_root  = false;
    static $default_parent='OneColumn';
    static $allowed_children = "none";
    static $db = array();

    static $defaults = array(
        "IncludeJquery" => 1,
        "IncludeShadowBox" => 1
    );

    function getCMSFields() {
        $fields = parent::getCMSFields();
        // remove unneeded fields
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