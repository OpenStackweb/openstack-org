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

    static $has_one = array(
        'Member' => 'Member',
        'Summit' => 'Summit'
    );

    static $many_many = array(
        'Categories' => 'PresentationCategory'
    );

    private static $summary_fields = array
    (
        'Member.Email'  => 'Member',
    );

    /**
     * @param $member
     * @param $category_id
     * @throws ValidationException
     * @throws null
     */
    public static function addChair($member, $category_id)
    {

        $priorChair = SummitTrackChair::get()->filter('MemberID', $member->ID)->first();
        $category   = PresentationCategory::get()->byID($category_id);

        if (!$priorChair) {
            $chair = new self();
            $chair->MemberID = $member->ID;
            $chair->SummitID = Summit::get_active()->ID;
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

    public function getCMSFields()
    {

        $summit_id = @$_REQUEST['SummitID'];

        $f = new FieldList(
            $rootTab = new TabSet("Root", $tabMain = new Tab('Main'))
        );

        $f->addFieldsToTab('Root.Main', new HiddenField('SummitID', 'SummitID'));
        $f->addFieldsToTab('Root.Main', new MemberAutoCompleteField('Member', 'Member'));

        if($this->ID > 0) {
            $config     = GridFieldConfig_RelationEditor::create(25);
            $config->getComponentByType('GridFieldAddExistingAutocompleter')->setSearchList(PresentationCategory::get()->filter('SummitID', $summit_id ));
            $categories = new GridField('Categories', 'Presentation Categories', $this->Categories(), $config);
            $f->addFieldToTab('Root.Presentation Categories', $categories);
        }
        return $f;
    }

    protected function validate(){

        $summit_id = $_REQUEST['SummitID'];
        $valid = parent::validate();
        if(!$valid->valid()) return $valid;
        $old_one = SummitTrackChair::get()->filter
        (
            array
            (
                'MemberID' => $this->MemberID,
                'SummitID' => $summit_id
            )
        )->first();

        if(!is_null($old_one))
        {
            return $valid->error('Already exists a track chair for this member!');
        }
        return $valid;
    }

}