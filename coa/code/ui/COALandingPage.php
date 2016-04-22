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
 * Class COALandingPage
 */
class COALandingPage extends Page
{
    static $db = array(
        'BannerText'   => 'HTMLText',
        'ExamDetails'  => 'HTMLText',
        'HandBookLink' => 'Text',
    );

    static $has_one = array
    (

    );

    static $many_many = array
    (
        'TrainingPartners' => 'Company'
    );

    static $many_many_extraFields = array(
        'TrainingPartners' => array(
            'Order' => "Int",
        ),
    );

    public function getBannerText(){
        $html = $this->getField('BannerText');
        if(empty($html)){
            $html = <<<HTML
<p>OpenStack skills are in high demand as thousands of companies around the world adopt and productize OpenStack.</p>
<p>Certified OpenStack Administrator (COA) is the first professional certification offered by the OpenStack Foundation. It’s designed to help companies identify top talent in the industry, and help job seekers demonstrate their skills.</p>
HTML;
        }
        return $html;
    }


    public function getExamDetails(){
        $html = $this->getField('ExamDetails');
        if(empty($html)){
            $html = <<<HTML
    <p>The Certified OpenStack Administrator is a professional typically with at least six months OpenStack experience, and has the skills required to provide day-to-day operation and management of an OpenStack cloud. To learn more about the knowledge requirements and domains covered in the exam, go to <a href="www.openstack.org/coa/requirements">www.openstack.org/coa/requirements</a>.</p>
HTML;
        }
        return $html;
    }

    public function getHandBookLink(){
        $html = $this->getField('HandBookLink');
        if(empty($html)){
            $html = <<<HTML
    #
HTML;
        }
        return $html;
    }

    function getCMSFields()
    {
        $fields = new FieldList(
            $rootTab = new TabSet("Root",   $tabMain = new Tab('Main'))
        );

        $fields->addFieldToTab('Root.Main', new HtmlEditorField('BannerText', 'Banner Text'));
        $fields->addFieldToTab('Root.Main', new HtmlEditorField('ExamDetails', 'Exam Details'));
        $fields->addFieldToTab('Root.Main', new TextField('HandBookLink', 'HandBook Link'));


        if ($this->ID > 0) {

            $config = GridFieldConfig_RelationEditor::create();

            $config->removeComponentsByType('GridFieldEditButton');
            $config->removeComponentsByType('GridFieldAddNewButton');
            $config->addComponent($sort = new GridFieldSortableRows('Order'));

            $partners = new GridField('TrainingPartners', 'Training Partners', $this->TrainingPartners(), $config);
            $fields->addFieldsToTab('Root.TrainingPartners',$partners);

        }

        return $fields;
    }


}

/**
 * Class COALandingPage_Controller
 */
class COALandingPage_Controller extends Page_Controller
{
    static $allowed_actions = array
    (
        'index',
        'getStarted',
        'alreadyRegistered'
    );

    static $url_handlers = array
    (
        'get-started'        => 'getStarted',
        'already-registered' => 'alreadyRegistered',
    );

    function init()
    {
        parent::init();
    }

    protected function handleAction($request, $action)
    {
        return parent::handleAction($request, $action);
    }

    public function index(){
        Requirements::css('coa/css/coa.css');
        return $this->getViewer('index')->process($this);
    }

    public function getStarted(){
        if(Member::currentUser()){
            $this->redirect(GET_STARTED_URL);
        }
        OpenStackIdCommon::doLogin($this->Link('get-started'));
    }

    public function alreadyRegistered(){
        if(Member::currentUser()){
            $this->redirect(ALREADY_REGISTERED_URL);
        }
        OpenStackIdCommon::doLogin($this->Link('already-registered'));
    }

}