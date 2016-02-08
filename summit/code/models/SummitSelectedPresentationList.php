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
class SummitSelectedPresentationList extends DataObject
{

    static $db = array
    (
        'Name'     => 'Text',
        'ListType' => "Enum('Individual,Group','Individual')"
    );

    static $has_one = array
    (
        'Category' => 'PresentationCategory',
        'Member'   => 'Member'
    );

    static $has_many = array(
        'SummitSelectedPresentations' => 'SummitSelectedPresentation'
    );

    private static $summary_fields = array
    (
        'Category.Title' => 'Name',
    );


    public function getCMSFields()
    {

        $f = new FieldList(
            $rootTab = new TabSet("Root", $tabMain = new Tab('Main'))
        );

        $f->addFieldToTab('Root.Main', new TextField('Name', 'Name'));
        $f->addFieldToTab('Root.Main', $ddl = new DropdownField('ListType', 'ListType',  $this->dbObject('ListType')->enumValues()));
        $f->addFieldToTab('Root.Main', $ddl2 = new DropdownField('CategoryID', 'Category',  PresentationCategory::get()->filter('SummitID', $_REQUEST['SummitID'] )->map('ID', 'Title')));
        $ddl->setEmptyString('-- Select List Type --');
        $ddl2->setEmptyString('-- Select Track  --');
        if($this->ID > 0)
        {
            $config     = GridFieldConfig_RecordEditor::create(25);

            $config->addComponent(new GridFieldAjaxRefresh(1000, false));
            $config->addComponent(new GridFieldPublishSummitEventAction);
            $config->removeComponentsByType('GridFieldDeleteAction');
            $config->removeComponentsByType('GridFieldAddNewButton');
            $config->addComponent($bulk_summit_types = new GridFieldBulkActionAssignSummitTypeSummitEvents);
            $bulk_summit_types->setTitle('Set Summit Types');

            $result = DB::query("SELECT DISTINCT SummitEvent.*, Presentation.*
FROM SummitEvent
INNER JOIN Presentation ON Presentation.ID = SummitEvent.ID
INNER JOIN SummitSelectedPresentation ON SummitSelectedPresentation.PresentationID = Presentation.ID
INNER JOIN SummitSelectedPresentationList ON SummitSelectedPresentation.SummitSelectedPresentationListID = {$this->ID}
ORDER BY SummitSelectedPresentation.Order ASC
");

            $presentations = new ArrayList();
            foreach ($result as $row) {
                $presentations->add(new Presentation($row));
            }

            $gridField = new GridField('SummitSelectedPresentations', 'Selected Presentations', $presentations , $config);
            $gridField->setModelClass('Presentation');
            $f->addFieldToTab('Root.Main', $gridField);

        }
        return $f;
    }


    function SortedPresentations()
    {
        return SummitSelectedPresentation::get()->filter(array(
            'SummitSelectedPresentationListID' => $this->ID,
            'Order:not' => 0
        ))->sort('Order', 'ASC');
    }

    function UnsortedPresentations()
    {
        return SummitSelectedPresentation::get()->filter(array(
            'SummitSelectedPresentationListID' => $this->ID,
            'Order' => 0
        ))->sort('Order', 'ASC');
    }

    function UnusedPostions()
    {

        // Define the columns
        $columnArray = array();

        $NumSlotsTaken = $this->SummitSelectedPresentations()->Count();
        $NumSlotsAvailable = $this->SummitCategory()->NumSessions - $NumSlotsTaken;

        $list = new ArrayList();

        for ($i = 0; $i < $NumSlotsAvailable; $i++) {
            $data = array('Name' => 'Available Slot');
            $list->push(new ArrayData($data));
        }

        return $list;

    }

    public static function getAllListsByCategory($SummitCategoryID)
    {

        $category = PresentationCategory::get()->byID($SummitCategoryID);

        // An empty array list that we'll use to return results
        $results = ArrayList::create();

        // Get any existing lists made for this category
        $AllLists = SummitSelectedPresentationList::get()
            ->filter('CategoryID', $SummitCategoryID)
            ->sort('ListType', 'ASC');

        // Filter to lists of any other track chairs
        $OtherTrackChairLists = $AllLists
            ->filter('ListType', 'Individual')
            ->exclude(
                'MemberID', Member::currentUser()->ID
            );

        $MemberList = $category->MemberList(Member::currentUser()->ID);
        $GroupList = $category->GroupList();

        if ($MemberList) {
            $results->push($MemberList);
        }
        foreach ($OtherTrackChairLists as $list) {
            $results->push($list);
        }
        if ($GroupList) {
            $results->push($GroupList);
        }

        // Add each of those lists to our results
        foreach ($results as $list) {

            if ($list->ListType == "Individual") {
                $list->name = $list->Member()->FirstName . ' ' . $list->Member()->Surname;
            }
            if ($list->ListType == "Group") {
                $list->name = 'Team Selections';
            }

        }

        return $results;
    }

    public function maxPresentations()
    {
        return $this->Category()->SessionCount;
    }

    public function memberCanEdit()
    {

        if (!Member::currentUser()) {
            return false;
        }

        if ($this->MemberID == Member::currentUser()->ID || $this->ListType == 'Group') {
            return true;
        }

    }

    public function mine()
    {
        return $this->MemberID == Member::currentUser()->ID;
    }

    public static function getMemberList($SummitCategoryID)
    {

        if (!Member::currentUser()) {
            return false;
        }

        $MemberID = Member::currentUser()->ID;

        $SummitSelectedPresentationList = SummitSelectedPresentationList::get()->filter(array(
            'CategoryID' => $SummitCategoryID,
            'ListType' => 'Individual',
            'MemberID' => Member::currentUser()->ID
        ))->first();;

        // if a summit talk list doesn't exist for this member and category, create it
        if (!$SummitSelectedPresentationList) {
            $SummitSelectedPresentationList = new SummitSelectedPresentationList();
            $SummitSelectedPresentationList->ListType = 'Individual';
            $SummitSelectedPresentationList->CategoryID = $SummitCategoryID;
            $SummitSelectedPresentationList->MemberID = Member::currentUser()->ID;
            $SummitSelectedPresentationList->write();
        }

        return $SummitSelectedPresentationList;

    }
}