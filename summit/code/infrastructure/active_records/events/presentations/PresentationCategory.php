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
/**
 * Class PresentationCategory
 */
class PresentationCategory extends DataObject
{

    private static $db = array
    (
        'Title'          => 'Varchar',
        'Description'    => 'Text',
        'SessionCount'   => 'Int',
        'AlternateCount' => 'Int',
        'VotingVisible'  => 'Boolean',
        'ChairVisible'   => 'Boolean',
        'Code'           => 'Varchar(5)',
        'Slug'           => 'Varchar',
    );

    private static $defaults = array
    (
        'VotingVisible' => true,
        'ChairVisible'  => true
    );

    private static $has_one = array(
        'Summit' => 'Summit'
    );

    private static $has_many = array(
        'ChangeRequests' => 'SummitCategoryChange',
    );

    private static $many_many = array(
        'AllowedTags' => 'Tag',
        'ExtraQuestions' => 'TrackQuestionTemplate',
    );

    private static $many_many_extraFields = array(
        'AllowedTags' => array(
            'Group' => "Enum('topics, speaker, openstack projects mentioned', 'topics')", // if change see also getcms
            'IsDefault' => "Boolean",
        ),
    );

    private static $belongs_many_many = array(
        'TrackChairs'   => 'SummitTrackChair',
        'CategoryGroup' => 'PresentationCategoryGroup'
    );

    private static $summary_fields = array(
        'Title' => 'Title'
    );

    private static $searchable_fields = array(
        'Title'
    );

    public function getCMSFields()
    {
        $fields = FieldList::create(TabSet::create('Root'))
            ->text('Title')
            ->text('Code','Code','',5)
            ->textarea('Description')
            ->numeric('SessionCount', 'Number of sessions')
            ->numeric('AlternateCount', 'Number of alternates')
            ->checkbox('VotingVisible', "This category is visible to voters")
            ->checkbox('ChairVisible', "This category is visible to track chairs")
            ->hidden('SummitID', 'SummitID');

        if($this->ID > 0)
        {
            //tags
            $config = new GridFieldConfig_RelationEditor(100);
            $config->removeComponentsByType(new GridFieldDataColumns());
            $config->removeComponentsByType(new GridFieldDetailForm());

            $completer = $config->getComponentByType('GridFieldAddExistingAutocompleter');
            $completer->setResultsFormat('$Tag');
            $completer->setSearchFields(array('Tag'));
            $completer->setSearchList(Tag::get());

            $editconf = new GridFieldDetailForm();
            $editconf->setFields(FieldList::create(
                TextField::create('Tag','Tag'),
                DropdownField::create('ManyMany[Group]', 'Group', array(
                    'topics' => 'Topics',
                    'speaker' => 'Speaker',
                    'openstack projects mentioned' => 'OpenStack Projects Mentioned'))
            ));

            $summaryfieldsconf = new GridFieldDataColumns();
            $summaryfieldsconf->setDisplayFields(array( 'Tag' => 'Tag', 'Group' => 'Group'));

            $config->addComponent($editconf);
            $config->addComponent($summaryfieldsconf, new GridFieldFilterHeader());

            $tags = new GridField('AllowedTags', 'Tags', $this->AllowedTags(), $config);
            $fields->addFieldToTab('Root.Main', $tags);

            // extra questions for call-for-presentations
            $config = new GridFieldConfig_RelationEditor();
            $config->removeComponentsByType('GridFieldAddNewButton');
            $multi_class_selector = new GridFieldAddNewMultiClass();

            $multi_class_selector->setClasses(
                array
                (
                    'TrackTextBoxQuestionTemplate'           => 'TextBox' ,
                    'TrackCheckBoxQuestionTemplate'          => 'CheckBox',
                    'TrackCheckBoxListQuestionTemplate'      => 'CheckBoxList',
                    'TrackRadioButtonListQuestionTemplate'   => 'RadioButtonList',
                    'TrackDropDownQuestionTemplate'          => 'ComboBox',
                    'TrackLiteralContentQuestionTemplate'    => 'Literal',
                )
            );
            $config->addComponent($multi_class_selector);
            $questions = new GridField('ExtraQuestions', 'Track Specific Questions', $this->ExtraQuestions(), $config);
            $fields->addFieldToTab('Root.Main', $questions);
        }

        return $fields;
    }

    public function hasEventsPublished(){
        return Presentation::get()->filter(["SummitID" => $this->SummitID, "Published" => 1 , "CategoryID" => $this->ID])->count() > 0;
    }

    protected function onBeforeWrite() {
        parent::onBeforeWrite();

        // populateDefaults not working, had to do it this way
        if (empty($this->Slug)) {
            $clean_title = preg_replace ("/[^a-zA-Z0-9 ]/", "", $this->Title);
            $this->Slug = preg_replace('/\s+/', '-', strtolower($clean_title));
        }
    }

    protected function onAfterWrite() {
        parent::onAfterWrite();
        $this->Summit()->LastEdited = SS_Datetime::now()->Rfc2822();
        $this->Summit()->write();
    }

    protected function validate()
    {
        $valid = parent::validate();
        if(!$valid->valid()) return $valid;

        $summit_id = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;

        $summit   = Summit::get()->byID($summit_id);

        if(!$summit){
            return $valid->error('Invalid Summit!');
        }

        $count = intval(PresentationCategory::get()->filter(array('SummitID' => $summit->ID, 'Title' => trim($this->Title), 'ID:ExactMatch:not' => $this->ID))->count());

        if($count > 0)
            return $valid->error(sprintf('Presentation Category "%s" already exists!. please set another one', $this->Title));

        return $valid;
    }

    public function getFormattedTitleAndDescription()
    {
        return '<h4 class="category-label">' . $this->Title . '</h4> <p>' . $this->Description . '</p>';
    }

    public function getCategoryGroups() {
        return $this->CategoryGroup();
    }

    /**
     * @param int $member_id
     * @return int
     */
    public function isTrackChair($member_id)
    {
        return $this->exists()? intval($this->TrackChairs()->filter('MemberID', $member_id)->count()):0;
    }

    public function MemberList($memberid)
    {

        // See if there's a list for the current member
        $MemberList = SummitSelectedPresentationList::get()->filter(array(
            'MemberID' => $memberid,
            'CategoryID' => $this->ID
        ))->first();

        // if a selection list doesn't exist for this member and category, create it
        if (!$MemberList && $this->isTrackChair($memberid)) {
            $MemberList = new SummitSelectedPresentationList();
            $MemberList->ListType = 'Individual';
            $MemberList->CategoryID = $this->ID;
            $MemberList->MemberID = $memberid;
            $MemberList->write();
        }

        if ($MemberList) {
            return $MemberList;
        }


    }

    public function GroupList()
    {

        // See if there's a list for the group
        $GroupList = SummitSelectedPresentationList::get()->filter(array(
            'ListType' => 'Group',
            'CategoryID' => $this->ID
        ))->first();

        // if a group selection list doesn't exist for this category, create it
        if (!$GroupList) {
            $GroupList = new SummitSelectedPresentationList();
            $GroupList->ListType = 'Group';
            $GroupList->CategoryID = $this->ID;
            $GroupList->write();
        }

        return $GroupList;

    }


    /**
     * @param Member $member
     * @return boolean
     */
    public function canView($member = null)
    {
        return Permission::check("ADMIN") || Permission::check("ADMIN_SUMMIT_APP") || Permission::check("ADMIN_SUMMIT_APP_SCHEDULE");
    }

    /**
     * @param Member $member
     * @return boolean
     */
    public function canEdit($member = null)
    {
        return Permission::check("ADMIN") || Permission::check("ADMIN_SUMMIT_APP") || Permission::check("ADMIN_SUMMIT_APP_SCHEDULE");
    }

}
