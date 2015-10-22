<?php

/**
 * Copyright 2015 OpenStack Foundation
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
class SoftwareSubPage extends Page
{
    static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');
}


class SoftwareSubPage_Controller extends Page_Controller
{
    public function init()
    {
        parent::init();
        Requirements::css("themes/openstack/bower_assets/webui-popover/dist/jquery.webui-popover.min.css");
        Requirements::css("software/css/software.css");
        Requirements::javascript("themes/openstack/bower_assets/webui-popover/dist/jquery.webui-popover.min.js");
    }

    public function getSubMenuPages(){
        return SoftwareHomePageSubMenuItem::get()->sort('Order','ASC');
    }

    public function getActive()
    {
        $base_index = 1;
        if($this->getHasAvailableSampleConfigTypes())
            $base_index = 2;
        $parent = SoftwareHomePage::get()->byID($this->ParentID);
        if(is_null($parent)) return -1;
        $pos = 0;
        foreach($parent->SubMenuPages()->sort('Order','ASC') as $sp)
        {
            ++$pos;
            if(strpos( $sp->Url, rtrim($this->data()->Link(true),'/')) !== false)
            {
                break;
            }
        }
        return $base_index+$pos;
    }

    public function getHasAvailableSampleConfigTypes()
    {
        $parent     = SoftwareHomePage::get()->byID($this->ParentID);
        if(is_null($parent)) return false;
        $controller = new SoftwareHomePage_Controller($parent);
        return $controller->HasAvailableSampleConfigTypes();
    }

    public function getCurrentIdx($pos)
    {
        $base_index = 1;
        if($this->getHasAvailableSampleConfigTypes())
            $base_index = 2;
        $pos = intval($pos);
        return $pos+$base_index;
    }

    public function Link($action= null)
    {
        $parent     = SoftwareHomePage::get()->byID($this->ParentID);
        if(is_null($parent)) return $action;
        $controller = new SoftwareHomePage_Controller($parent);
        return $controller->Link($action);
    }
}