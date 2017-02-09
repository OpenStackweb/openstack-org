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

    const Individual = 'Individual';
    const Group = 'Group';
    const Session = 'Session';
    const Lightning = 'Lightning';

    /**
     * @var array
     */
    private static $db = [
        'Name' => 'Text',
        'ListType' => "Enum('Individual,Group','Individual')",
        'ListClass' => "Enum('Session,Lightning','Session')"
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'Category' => 'PresentationCategory',
        'Member' => 'Member'
    ];

    /**
     * @var array
     */
    private static $has_many = [
        'SummitSelectedPresentations' => 'SummitSelectedPresentation'
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'Category.Title' => 'Name',
    ];


    /**
     * @return FieldList
     */
    public function getCMSFields()
    {

        $f = new FieldList(
            $rootTab = new TabSet("Root", $tabMain = new Tab('Main'))
        );

        $f->addFieldToTab('Root.Main', new TextField('Name', 'Name'));
        $f->addFieldToTab('Root.Main',
            $ddl = new DropdownField('ListType', 'ListType', $this->dbObject('ListType')->enumValues()));
        $f->addFieldToTab('Root.Main', $ddl2 = new DropdownField('CategoryID', 'Category',
            PresentationCategory::get()->filter('SummitID', $_REQUEST['SummitID'])->map('ID', 'Title')));
        $ddl->setEmptyString('-- Select List Type --');
        $ddl2->setEmptyString('-- Select Track  --');
        if ($this->ID > 0) {
            $config = GridFieldConfig_RecordEditor::create(25);

            $config->addComponent(new GridFieldAjaxRefresh(1000, false));
            $config->addComponent(new GridFieldPublishSummitEventAction);
            $config->removeComponentsByType('GridFieldDeleteAction');
            $config->removeComponentsByType('GridFieldAddNewButton');

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

            $gridField = new GridField('SummitSelectedPresentations', 'Selected Presentations', $presentations,
                $config);
            $gridField->setModelClass('Presentation');
            $f->addFieldToTab('Root.Main', $gridField);

        }
        return $f;
    }


    /**
     * @return mixed
     */
    public function SortedPresentations()
    {
        return SummitSelectedPresentation::get()->filter(array(
            'SummitSelectedPresentationListID' => $this->ID,
            'Order:not' => 0
        ))->sort('Order', 'ASC');
    }

    /**
     * @return mixed
     */
    public function UnsortedPresentations()
    {
        return SummitSelectedPresentation::get()->filter(array(
            'SummitSelectedPresentationListID' => $this->ID,
            'Order' => 0
        ))->sort('Order', 'ASC');
    }

    /**
     * @return ArrayList
     */
    public function UnusedPostions()
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

    /**
     * @param $SummitCategoryID
     * @return mixed
     */
    public static function getAllListsByCategory($SummitCategoryID, $ListClass = SummitSelectedPresentationList::Session)
    {

        $category = PresentationCategory::get()->byID($SummitCategoryID);

        // An empty array list that we'll use to return results
        $results = ArrayList::create();

        // Get any existing lists made for this category
        $AllLists = SummitSelectedPresentationList::get()
            ->filter([
                'CategoryID' => $SummitCategoryID,
                'ListClass' => $ListClass
            ])
            ->sort('ListType', 'ASC');

        // Filter to lists of any other track chairs
        $OtherTrackChairLists = $AllLists
            ->filter('ListType', SummitSelectedPresentationList::Individual)
            ->exclude(
                'MemberID', Member::currentUser()->ID
            );

        $MemberList = $category->MemberList(Member::currentUser()->ID, $ListClass);
        $GroupList = $category->GroupList($ListClass);

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

            if (!$list->isGroup()) {
                $list->name = $list->Member()->FirstName . ' ' . $list->Member()->Surname;
            }
            if ($list->isGroup()) {
                $list->name = 'Team Selections';
            }

        }

        return $results;
    }

    /**
     * @return mixed
     */
    public function maxPresentations()
    {
        if ($this->isLightning())
            return $this->Category()->LightningCount + $this->Category()->LightningAlternateCount;
        else
            return $this->Category()->SessionCount + $this->Category()->AlternateCount;
    }

    public function maxAlternates()
    {
        if ($this->isLightning())
            return $this->Category()->LightningAlternateCount;
        else
            return $this->Category()->AlternateCount;
    }

    /**
     * @return bool
     */
    public function memberCanEdit()
    {

        if (!Member::currentUser()) {
            return false;
        }

        if ($this->MemberID == Member::currentUser()->ID || $this->isGroup()) {
            return true;
        }

    }

    /**
     * @return bool
     */
    public function mine()
    {
        return $this->MemberID == Member::currentUser()->ID;
    }

    /**
     * @param $SummitCategoryID
     * @return bool|SummitSelectedPresentationList
     */
    public static function getMemberList($SummitCategoryID, $ListClass = SummitSelectedPresentationList::Session)
    {

        if (!Member::currentUser()) {
            return false;
        }

        $SummitSelectedPresentationList = SummitSelectedPresentationList::get()->filter(array(
            'CategoryID' => $SummitCategoryID,
            'ListType' => SummitSelectedPresentationList::Individual,
            'ListClass' => $ListClass,
            'MemberID' => Member::currentUser()->ID
        ))->first();;

        // if a summit talk list doesn't exist for this member and category, create it
        if (!$SummitSelectedPresentationList) {
            $SummitSelectedPresentationList = new SummitSelectedPresentationList();
            $SummitSelectedPresentationList->ListType = SummitSelectedPresentationList::Individual;
            $SummitSelectedPresentationList->ListClass = $ListClass;
            $SummitSelectedPresentationList->CategoryID = $SummitCategoryID;
            $SummitSelectedPresentationList->MemberID = Member::currentUser()->ID;
            $SummitSelectedPresentationList->write();
        }

        return $SummitSelectedPresentationList;
    }

    public function isGroup() {
        return ($this->ListType == SummitSelectedPresentationList::Group);
    }

    public function isLightning() {
        return ($this->ListClass == SummitSelectedPresentationList::Lightning);
    }

    public static function getListClassName($list_class) {
        return ($list_class == SummitSelectedPresentationList::Session) ? 'Presentation' : 'Lightning';
    }
}