<?php
/**
 * Created by JetBrains PhpStorm.
 * User: smarcet
 * Date: 8/15/13
 * Time: 5:37 PM
 * To change this template use File | Settings | File Templates.
 */

class BoardOfDirectorsPage extends Page{
    static $can_be_root  = false;
    static $default_parent='OneColumn';
    static $allowed_children = "none";
    static $db = array();

    function getCMSFields() {
        $fields = parent::getCMSFields();
        // remove unneeded fields
        return $fields;
    }
}

class BoardOfDirectorsPage_Controller extends  Page_Controller{

    public function BoardOfDirectorsMembers(){
        //Group_Member
        $group = Group::get()->filter('Code','board-of-directors')->first();
        $res= $group->getManyManyComponents("Members",'','Member.FirstName, Member.SurName');
        return $res;
    }

    public function init() {
        parent::init();
        Requirements::css(THEMES_DIR ."/openstack/css/bio.css");
    }
}