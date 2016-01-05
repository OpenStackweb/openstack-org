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
class PresentationCategoryGroup extends DataObject
{
    private static $db = array
    (
        'Name'        => 'Text',
        'Color'       => 'Varchar',
        'Description' => 'HTMLText',
        'StartDate'   => 'SS_Datetime',
        'EndDate'     => 'SS_Datetime'
    );

    private static $defaults = array
    (
    );

    private static $many_many = array
    (
        'Categories' => 'PresentationCategory'
    );

    private static $has_one = array
    (
        'Summit'   => 'Summit'
    );

    private static $summary_fields = array
    (
        'Name'        => 'Name',
        'Color'       => 'Color',
        'Description' => 'Description',
        'StartDate'   => 'Start Date',
        'EndDate'     => 'End Date'
    );

    private static $searchable_fields = array
    (
        'Name'
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    public function getCMSFields()
    {
        $summit_id = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;

        $f = new FieldList
        (
            $rootTab = new TabSet("Root", $tabMain = new Tab('Main'))
        );

        $f->addFieldToTab('Root.Main', new TextField('Name','Name'));
        $f->addFieldToTab('Root.Main', new TextField('Color','Color'));

        $f->addFieldToTab('Root.Main',$date = new DateField('StartDate', 'Start Date'));
        $date->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');

        $f->addFieldToTab('Root.Main',$date = new DateField('EndDate', 'End Date'));
        $date->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');

        if($this->ID > 0) {
            $config = new GridFieldConfig_RelationEditor(100);
            $config->removeComponentsByType('GridFieldEditButton');
            $config->removeComponentsByType('GridFieldAddNewButton');
            $config->addComponent(new GridFieldDeleteAction('unlinkrelation'));
            $completer = $config->getComponentByType('GridFieldAddExistingAutocompleter');
            $completer->setSearchList(PresentationCategory::get()->filter('SummitID', $summit_id));
            $categories = new GridField('Categories', 'Presentation Category', $this->Categories(), $config);
            $f->addFieldToTab('Root.Main', $categories);
        }

        $f->addFieldToTab('Root.Main', new HtmlEditorField('Description','Description'));

        $f->addFieldToTab('Root.Main', new HiddenField('SummitID','SummitID'));


        return $f;
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

        $count = intval(PresentationCategoryGroup::get()->filter(array('SummitID' => $summit->ID, 'Name' => trim($this->Name), 'ID:ExactMatch:not' => $this->ID))->count());

        if($count > 0)
            return $valid->error(sprintf('Presentation Category Group "%s" already exists!. please set another one', $this->Name));

        $start_date = $this->getStartDate();
        $end_date   = $this->getEndDate();

        if((empty($start_date) || empty($end_date))) {
            return $valid->error('To publish this group you must define a start/end dates!');
        } else {
            $timezone = $summit->TimeZone;

            if(empty($timezone)){
                return $valid->error('Invalid Summit TimeZone!');
            }

            $start_date = new DateTime($start_date);
            $end_date   = new DateTime($end_date);

            if($end_date < $start_date)
                return $valid->error('start datetime must be greather or equal than end datetime!');

            if(!$summit->isEventInsideSummitDuration($this))
                return $valid->error(sprintf('start/end date must be between summit start/end datetime! (%s - %s)', $summit->getBeginDate(), $summit->getEndDate()));
        }

        return $valid;
    }

    public function getStartDate()
    {
        $summit_id  = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;
        $summit     = Summit::get()->byID($summit_id);
        if(is_null($summit)) throw new InvalidArgumentException('summit not found!');
        $value = $this->getField('StartDate');
        return $summit->convertDateFromUTC2TimeZone($value);
    }

    public function getEndDate()
    {
        $summit_id  = isset($_REQUEST['SummitID']) ?  $_REQUEST['SummitID'] : $this->SummitID;
        $summit     = Summit::get()->byID($summit_id);
        if(is_null($summit)) throw new InvalidArgumentException('summit not found!');
        $value = $this->getField('EndDate');
        return $summit->convertDateFromUTC2TimeZone($value);
    }

}