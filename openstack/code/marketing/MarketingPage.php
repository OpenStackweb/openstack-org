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
class MarketingPage extends Page{
	
	private static $db = array(
        'HeaderTitle'             => 'Varchar(255)',
        'HeaderText'              => 'HTMLText',
        'InvolvedText'            => 'HTMLText',
        'EventsIntroText'         => 'HTMLText',
        'CollateralIntroText'     => 'HTMLText',
        'SoftwareIntroText'       => 'HTMLText',
        'GraphicsIntroText'       => 'HTMLText',
        'PromoteProductIntroText' => 'HTMLText',
	);

	private static $has_many = array(
        'InvolvedImages' => 'MarketingImage.InvolvedImages',
        'SponsorEvents'  => 'MarketingEvent.SponsorEvents',
        'Collaterals'    => 'MarketingCollateral',
        'Software'       => 'MarketingSoftware',
        'Stickers'       => 'MarketingDoc.Stickers',
        'TShirts'        => 'MarketingDoc.TShirts',
        'Banners'        => 'MarketingDoc.Banners',
        'Videos'         => 'MarketingVideo.Videos',
        'PromoteEvents'  => 'MarketingEvent.PromoteEvents',
	);
	
	function getCMSFields(){

        $fields = parent::getCMSFields();
        $fields->insertBefore(new Tab('GetInvolved'), 'Settings');
        $fields->insertBefore(new Tab('Events'), 'Settings');
        $fields->insertBefore(new Tab('Collateral'), 'Settings');
        $fields->insertBefore(new Tab('Software'), 'Settings');
        $fields->insertBefore(new Tab('Graphics'), 'Settings');
        $fields->insertBefore(new Tab('Promote'), 'Settings');

        // header
        $fields->removeByName('Content');
        $fields->addFieldToTab('Root.Main', new TextField('HeaderTitle','Header Title'));
        $fields->addFieldToTab('Root.Main', new HtmlEditorField('HeaderText','Header Text'));

        // Get Involved
        $fields->addFieldToTab(
            'Root.GetInvolved',
            $involved_images = new UploadField('InvolvedImages', 'Involved Images')
        );
        $involved_images->setFolderName('marketing');

        $fields->addFieldToTab(
            'Root.GetInvolved',
            new HtmlEditorField('InvolvedText','Involved Text',$this->InvolvedText)
        );

        // Events
        $fields->addFieldToTab(
            'Root.Events',
            new HtmlEditorField('EventsIntroText','Events Intro Text',$this->EventsIntroText)
        );
        $config = new GridFieldConfig_RecordEditor(3);
        $config->addComponent(new GridFieldSortableRows('SortOrder'));
        $fields->addFieldToTab(
            'Root.Events',
            new GridField('SponsorEvents', 'SponsorEvents', $this->SponsorEvents(), $config)
        );


        // Collateral
        $fields->addFieldToTab(
            'Root.Collateral',
            new HtmlEditorField('CollateralIntroText','Collateral Intro Text',$this->CollateralIntroText)
        );
        $config = new GridFieldConfig_RecordEditor(3);
        $config->addComponent(new GridFieldSortableRows('SortOrder'));
        $fields->addFieldToTab(
            'Root.Collateral',
            new GridField('Collaterals', 'Collaterals', $this->Collaterals(), $config)
        );

        // Software
        $fields->addFieldToTab(
            'Root.Software',
            new HtmlEditorField('SoftwareIntroText','Software Intro Text',$this->SoftwareIntroText)
        );
        $config = new GridFieldConfig_RecordEditor(3);
        $config->addComponent(new GridFieldSortableRows('SortOrder'));
        $fields->addFieldToTab(
            'Root.Software',
            new GridField('Software', 'Software', $this->Software(), $config)
        );

        // Graphics
        $fields->addFieldToTab(
            'Root.Graphics',
            new HtmlEditorField('GraphicsIntroText','Graphics Intro Text',$this->GraphicsIntroText)
        );
        $config = new GridFieldConfig_RecordEditor(3);
        $config->addComponent(new GridFieldSortableRows('SortOrder'));
        $fields->addFieldToTab(
            'Root.Graphics',
            new GridField('Stickers', 'Stickers', $this->Stickers(), $config)
        );
        $config = new GridFieldConfig_RecordEditor(3);
        $config->addComponent(new GridFieldSortableRows('SortOrder'));
        $fields->addFieldToTab(
            'Root.Graphics',
            new GridField('TShirts', 'TShirts', $this->TShirts(), $config)
        );
        $config = new GridFieldConfig_RecordEditor(3);
        $config->addComponent(new GridFieldSortableRows('SortOrder'));
        $fields->addFieldToTab(
            'Root.Graphics',
            new GridField('Banners', 'Banners', $this->Banners(), $config)
        );
        $config = new GridFieldConfig_RecordEditor(3);
        $config->addComponent(new GridFieldSortableRows('SortOrder'));
        $fields->addFieldToTab(
            'Root.Graphics',
            new GridField('Videos', 'Videos', $this->Videos(), $config)
        );

        // Promote
        $fields->addFieldToTab(
            'Root.Promote',
            $about_desc = new HtmlEditorField('PromoteProductIntroText','Promote Intro Text',$this->PromoteProductIntroText)
        );
        $config = new GridFieldConfig_RecordEditor(3);
        $config->addComponent(new GridFieldSortableRows('SortOrder'));
        $fields->addFieldToTab(
            'Root.Promote',
            new GridField('PromoteEvents', 'PromoteEvents', $this->PromoteEvents(), $config)
        );

        return $fields;
	}
}

class MarketingPage_Controller extends Page_Controller{

    function init() {
        parent::init();
        Requirements::css('software/css/software.css');
        Requirements::css('themes/openstack/css/marketing-page.css');
        Requirements::javascript('themes/openstack/javascript/urlfragment.jquery.js');

        Requirements::customScript("
            $(document).ready(function(){
                var tab = $(window).url_fragment('getParam','tab');
                if(tab !== null) {
                    if ($('#'+tab).length) {
                        $('.active').removeClass('active');
                        $('#'+tab).addClass('active');
                        $('.tab-'+tab).addClass('active');
                    }
                }

                $('.nav-link').click(function(){
                    var tab_id = $($(this).attr('href')).attr('id');
                    $(window).url_fragment('setParam','tab', tab_id);
                    window.location.hash = $(window).url_fragment('serialize');
                });

            });
        ");
    }

    function getStickersGrouped() {
        $result_array = array();
        foreach ($this->Stickers() as $sticker) {
            if($sticker->GroupName) {
                if (!isset($result_array[$sticker->GroupName])) {
                    $result_array[$sticker->GroupName] = array();
                }
                $result_array[$sticker->GroupName][] = $sticker;

            } else {
                $result_array['single'][] = $sticker;
            }
        }

        $result = ArrayList::create();
        foreach ($result_array as $group => $items)
        {
            $group_list = new ArrayData(array('Group' => $group, 'GroupID' => str_replace(' ','_',$group), 'Items' => new ArrayList($items)));
            $result->push($group_list);
        }

        return $result;
    }

    function getTShirtsGrouped() {
        $result_array = array();
        foreach ($this->TShirts() as $shirt) {
            if($shirt->GroupName) {
                if (!isset($result_array[$shirt->GroupName])) {
                    $result_array[$shirt->GroupName] = array();
                }
                $result_array[$shirt->GroupName][] = $shirt;

            } else {
                $result_array['single'][] = $shirt;
            }
        }

        $result = ArrayList::create();
        foreach ($result_array as $group => $items)
        {
            $group_list = new ArrayData(array('Group' => $group, 'GroupID' => str_replace(' ','_',$group), 'Items' => new ArrayList($items)));
            $result->push($group_list);
        }

        return $result;
    }

    function getBannersGrouped() {
        $result_array = array();
        foreach ($this->Banners() as $banner) {
            if($banner->GroupName) {
                if (!isset($result_array[$banner->GroupName])) {
                    $result_array[$banner->GroupName] = array();
                }
                $result_array[$banner->GroupName][] = $banner;

            } else {
                $result_array['single'][] = $banner;
            }
        }

        $result = ArrayList::create();
        foreach ($result_array as $group => $items)
        {
            $group_list = new ArrayData(array('Group' => $group, 'GroupID' => str_replace(' ','_',$group), 'Items' => new ArrayList($items)));
            $result->push($group_list);
        }

        return $result;
    }

}