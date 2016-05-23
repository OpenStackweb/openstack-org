<?php

/**
 * Copyright 2016 OpenStack Foundation
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
 * Class COAVerifyPage
 */
class COAVerifyPage extends Page
{
    static $db = array(
        'TosText'   => 'HTMLText',
    );

    static $has_one = array
    (

    );

    static $many_many = array
    (
    );

    static $many_many_extraFields = array(
    );



    function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('Content');
        $fields->addFieldToTab('Root.Main', new HtmlEditorField('TosText', 'Terms Of Service Text'));

        return $fields;
    }


}

/**
 * Class COAVerifyPage_Controller
 */
class COAVerifyPage_Controller extends Page_Controller
{
    static $allowed_actions = array
    (
    );

    static $url_handlers = array
    (
    );

    function init()
    {
        parent::init();

        Requirements::css('coa/css/coa-verify.css');
        Requirements::javascript('coa/js/coa-verify.js');
        Requirements::css('themes/openstack/bower_assets/sweetalert/dist/sweetalert.css');
        Requirements::javascript('themes/openstack/bower_assets/sweetalert/dist/sweetalert.min.js');
    }

    public function index(){

        return $this->getViewer('index')->process($this);
    }

}