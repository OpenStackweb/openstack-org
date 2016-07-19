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
class SummitTrackChair extends DataObject
{

    /**
     * @var array
     */
    private static $has_one = [
        'Member' => 'Member',
        'Summit' => 'Summit'
    ];

    /**
     * @var array
     */
    private static $many_many = [
        'Categories' => 'PresentationCategory'
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'Member.Email' => 'Member',
    ];

    /**
     * @param $member
     * @param $category_id
     * @throws ValidationException
     * @throws null
     */
    public static function addChair($member, $category_id)
    {

        $priorChair = SummitTrackChair::get()->filter('MemberID', $member->ID)->first();
        $category = PresentationCategory::get()->byID($category_id);

        if (!$priorChair) {
            $chair = new self();
            $chair->MemberID = $member->ID;
            $chair->write();
            $chair->Categories()->add($category);

            //Find or create the 'track-chairs' group
            if (!$Group = Group::get()->filter('Code', 'track-chairs')->first()) {
                $Group = new Group();
                $Group->Code = "track-chairs";
                $Group->Title = "Track Chairs";
                $Group->Write();
                $member->Groups()->add($Group);
            }
            //Add member to the group
            $member->Groups()->add($Group);

        } else {
            $priorChair->Categories()->add($category);
        }
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {

        $summit_id = @$_REQUEST['SummitID'];

        $f = new FieldList(
            $rootTab = new TabSet("Root", $tabMain = new Tab('Main'))
        );

        $f->addFieldsToTab('Root.Main', new HiddenField('SummitID', 'SummitID'));
        $f->addFieldsToTab('Root.Main', new MemberAutoCompleteField('Member', 'Member'));

        if ($this->ID > 0) {
            $config = GridFieldConfig_RelationEditor::create(25);
            $config->getComponentByType('GridFieldAddExistingAutocompleter')->setSearchList(PresentationCategory::get()->filter('SummitID',
                $summit_id));
            $categories = new GridField('Categories', 'Presentation Categories', $this->Categories(), $config);
            $f->addFieldToTab('Root.Presentation Categories', $categories);
        }
        return $f;
    }

    /**
     * @return mixed
     */
    protected function validate()
    {
    	$summit_id = null;
        if(isset($_REQUEST['SummitID'])) {
        	$summit_id = $_REQUEST['SummitID'];
        }
        
        $valid = parent::validate();
        
        if (!$valid->valid() || !$summit_id) {
            return $valid;
        }
        
        $old_one = Summit::get_active()
        	->Categories()
        	->relation('TrackChairs')
        	->filter('MemberID', $this->MemberID)
        	->first();

        if (!$old_one) {
            return $valid->error('Already exists a track chair for this member!');
        }

        return $valid;
    }

}