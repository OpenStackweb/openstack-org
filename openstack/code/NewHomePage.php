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
        // CLOUD INFRASTRUCTURE BLOCK
        'CloudInfraTitle'   => 'Text',
        'CloudInfraContent' => 'HTMLText',
        'CloudInfraLink' => 'Text',
        // LATEST RELEASE BLOCK
        "LatestReleaseName"  => 'Text',
        "LatestReleaseVideoLink"  => 'Text',
        "LatestReleaseVideoDescription"  => 'Text',
        "LatestReleaseCurrentButtonText"  => 'Text',
        "LatestReleaseCurrentButtonLink"  => 'Text',
        "LatestReleaseUpNextButtonText"  => 'Text',
        "LatestReleaseUpNextButtonLink"  => 'Text',
    ];

    private static $has_one  = [
        'LatestReleaseVideoPoster' => 'CloudImage',
    ];

    private static $has_many = [
        'OSFMembers' => 'OSFMember',
    ];

    private static $many_many = [
        'MarketplaceSpotLight' => 'CompanyService',
        'UserStories' => 'UserStoryDO',
    ];

    private static $many_many_extraFields = [
        'UserStories'  =>[
            'ButtonText' => 'Text',
        ]
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

    public function getLatestReleaseName(){
        $res = $this->getField('LatestReleaseName');
        if(empty($res)){
            $res = 'OpenStack Ussuri';
        }
        return $res;
    }

    public function getLatestReleaseVideoLink(){
        $res = $this->getField('LatestReleaseVideoLink');
        if(empty($res)){
            $res = '#';
        }
        return $res;
    }

    public function getLatestReleaseVideoDescription(){
        $res = $this->getField('LatestReleaseVideoDescription');
        if(empty($res)){
            $res = 'Watch the Ussuri Community Meeting';
        }
        return $res;
    }

    public function getLatestReleaseCurrentButtonText(){
        $res = $this->getField('LatestReleaseCurrentButtonText');
        if(empty($res)){
            $res = 'THIS RELEASE: USSURI';
        }
        return $res;
    }

    public function getLatestReleaseCurrentButtonLink(){
        $res = $this->getField('LatestReleaseCurrentButtonLink');
        if(empty($res)){
            $res = '#';
        }
        return $res;
    }

    public function getLatestReleaseUpNextButtonText(){
        $res = $this->getField('LatestReleaseUpNextButtonText');
        if(empty($res)){
            $res = 'UP NEXT: VICTORIA';
        }
        return $res;
    }

    public function getLatestReleaseUpNextButtonLink(){
        $res = $this->getField('LatestReleaseUpNextButtonLink');
        if(empty($res)){
            $res = '#';
        }
        return $res;
    }

    public function getLatestReleaseVideoPosterUrl(){
        if($this->LatestReleaseVideoPoster()->exists())
        {
            return $this->LatestReleaseVideoPoster()->Link();
        }
        return '/themes/openstack/home_images/Ussuri/Ussuri_1920x1080.jpg';
    }

    public function getRandomCompanyService(){
        $list = $this->MarketplaceSpotLight()->toArray();
        if(!count($list)) return null;
        $index = array_rand ($list);
        return $list[$index];
    }

    public function getCompanyServiceMarketplaceLink($id):?string{
       return '#';
    }

    public function getRandomOSFMember(){
        $list = $this->OSFMembers()->toArray();
        if(!count($list)) return null;
        $index = array_rand ($list);
        return $list[$index];
    }

    function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // CLOUD INFRASTRUCTURE BLOCK
        $fields->addFieldToTab('Root.CLOUD INFRASTRUCTURE BLOCK', new TextField ('CloudInfraTitle', 'Title'));

        $fields->addFieldToTab('Root.CLOUD INFRASTRUCTURE BLOCK', new HtmlEditorField ('CloudInfraContent', 'Content'));

        $fields->addFieldToTab('Root.CLOUD INFRASTRUCTURE BLOCK', new TextField ('CloudInfraLink', 'Link (READ MORE)'));

        // LATEST RELEASE BLOCK

        $fields->addFieldToTab('Root.LATEST RELEASE BLOCK', new TextField ('LatestReleaseName', 'Name'));

        $fields->addFieldToTab('Root.LATEST RELEASE BLOCK', new TextField ('LatestReleaseVideoLink', 'Video Link'));

        $promo_hero_image  = new CustomUploadField('LatestReleaseVideoPoster', 'Video Poster');
        $promo_hero_image->setFolderName('homepage');
        $promo_hero_image->setAllowedFileCategories('image');

        $fields->addFieldToTab('Root.LATEST RELEASE BLOCK', $promo_hero_image);

        $fields->addFieldToTab('Root.LATEST RELEASE BLOCK', new TextField ('LatestReleaseVideoDescription', 'Video Description'));

        $fields->addFieldToTab('Root.LATEST RELEASE BLOCK', new TextField ('LatestReleaseCurrentButtonText', 'Current Button Text'));

        $fields->addFieldToTab('Root.LATEST RELEASE BLOCK', new TextField ('LatestReleaseCurrentButtonLink', 'Current Button Link'));

        $fields->addFieldToTab('Root.LATEST RELEASE BLOCK', new TextField ('LatestReleaseUpNextButtonText', 'UpNext Button Text'));

        $fields->addFieldToTab('Root.LATEST RELEASE BLOCK', new TextField ('LatestReleaseUpNextButtonLink', 'UpNext Button Link'));

        // MARKETPLACE SPOTLIGHT

        $config = GridFieldConfig_RelationEditor::create(50);
        $config->removeComponentsByType("GridFieldAddNewButton");
        $completer = $config->getComponentByType('GridFieldAddExistingAutocompleter');
        $completer->setResultsFormat('$Name ($ID)');
        $completer->setSearchFields(['Name', 'ID']);
        $config->getComponentByType('GridFieldDataColumns')->setDisplayFields
        (
            [
                'ID' => 'ID',
                'Name'  => 'Name',
            ]
        );

        $completer->setSearchList(CompanyService::get()->filter(['Active' => true]));
        $services = new GridField('MarketplaceSpotLight',
            'Marketplace SpotLight',
            $this->MarketplaceSpotLight(), $config);
        $fields->addFieldToTab('Root.MARKETPLACE SPOTLIGHT', $services);

        // USER STORIES
        $userStoryFields = singleton('UserStoryDO')->getCMSFields();

        $oldFields = $userStoryFields->toArray();
        foreach($oldFields as $field){
            $userStoryFields->remove($field);
        }

        $userStoryFields->add($rootTab = new TabSet("Root", $tabMain = new Tab('Main')));

        $homePageImage  = new CustomUploadField('HomePageImage', 'Home Page Image');
        $homePageImage->setFolderName('homepage');
        $homePageImage->setAllowedFileCategories('image');

        $userStoryFields->addFieldsToTab('Root.Main', array(
            new ReadonlyField('Name', 'Name'),
            new TextField('ManyMany[ButtonText]', 'Button Text', ''),
            $homePageImage
        ));

        $config = GridFieldConfig_RelationEditor::create(50);
        $config->removeComponentsByType("GridFieldAddNewButton");
        $config->getComponentByType('GridFieldDetailForm')->setFields($userStoryFields);
        $completer = $config->getComponentByType('GridFieldAddExistingAutocompleter');
        $completer->setResultsFormat('$Name ($ID)');
        $completer->setSearchFields(['Name', 'ID']);
        $config->getComponentByType('GridFieldDataColumns')->setDisplayFields
        (
            [
                'ID' => 'ID',
                'Name'  => 'Name',
            ]
        );

        $completer->setSearchList(UserStoryDO::get()->filter(['Active' => true]));
        $services = new GridField('UserStories',
            'User Stories',
            $this->UserStories(), $config);
        $fields->addFieldToTab('Root.USER STORIES BLOCK', $services);


        // OSF MEMBER SPOTLIGHT

        $config = GridFieldConfig_RecordEditor::create(50);
        $config->addComponent(new GridFieldAjaxRefresh(1000, false));
        $config->removeComponentsByType('GridFieldDeleteAction');
        $gridField = new GridField('OSFMembers', 'OSFMembers', $this->OSFMembers(), $config);
        $config->getComponentByType("GridFieldDataColumns")->setFieldCasting(array("Description" => "HTMLText->BigSummary"));
        $fields->addFieldToTab('Root.OSF MEMBER SPOTLIGHT', $gridField);

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
}