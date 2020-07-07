<?php
/**
 * Copyright 2020 OpenStack Foundation
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

class NewHomePage extends Page
{
    private static $db = [
        'CloudInfraTitle'   => 'Text',
        'CloudInfraContent' => 'HTMLText',
        'CloudInfraLink' => 'Text',
    ];

    public function getCloudInfraTitle():string{
        $res = $this->getField('CloudInfraTitle');
        if(empty($res)){
            $res = <<<HTML
Cloud Infrastructure for Virtual Machines, Bare Metal, and Containers
HTML;

        }
        return $res;
    }

    public function getCloudInfraContent():string{
        $res = $this->getField('CloudInfraContent');
        if(empty($res)){
            $res = <<<HTML
Openstack controls large pools of compute, storage, and networking resources,
all managed through APIs or a dashboard. Beyond standard infrastructure-as-a-service
functionality, additional components provide orchestration, fault management and
service management amongst other services to ensure high availability of user
applications.
HTML;

        }
        return $res;
    }

    public function getCloudInfraLink(){
        $res = $this->getField('CloudInfraLink');
        if(empty($res)){
            $res = '#';
        }
        return $res;
    }

    function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab('Root.CLOUD INFRASTRUCTURE BLOCK', new TextField ('CloudInfraTitle', 'Title'));

        $fields->addFieldToTab('Root.CLOUD INFRASTRUCTURE BLOCK', new HtmlEditorField ('CloudInfraContent', 'Content'));

        $fields->addFieldToTab('Root.CLOUD INFRASTRUCTURE BLOCK', new TextField ('CloudInfraLink', 'Link (READ MORE)'));


        return $fields;
    }
}

class NewHomePage_Controller extends Page_Controller
{
    protected static function getCssIncludes(){
        return [
            "themes/openstack/css/home-page.css",
            "themes/openstack/css/navigation_menu.css",
            "themes/openstack/css/dropdown.css",
        ];
    }

    public function getUserStories(){
        return UserStoryDO::get()->filter(['ShowAtHomePage' => true]);
    }
}