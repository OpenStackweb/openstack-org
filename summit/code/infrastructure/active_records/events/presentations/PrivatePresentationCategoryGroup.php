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
 * Class PrivatePresentationCategoryGroup
 */
class PrivatePresentationCategoryGroup extends PresentationCategoryGroup
{
    private static $db = array
    (
        'SubmissionBeginDate'         => 'SS_Datetime',
        'SubmissionEndDate'           => 'SS_Datetime',
        'MaxSubmissionAllowedPerUser' => 'Int',
    );

    private static $defaults = array
    (
    );

    // only users that belongs to this groups could use this category group
    private static $many_many = array
    (
        'AllowedGroups' => 'Group'
    );

    /**
     * @return bool
     */
    public function isSubmissionOpen()
    {
        $start_date = $this->getField('SubmissionBeginDate');
        $end_date   = $this->getField('SubmissionEndDate');

        if (empty($start_date) || empty($end_date))
        {
            return false;
        }

        $start_date = new DateTime($start_date, new DateTimeZone('UTC'));
        $end_date   = new DateTime($end_date, new DateTimeZone('UTC'));
        $now        = new DateTime('now', new DateTimeZone('UTC'));

        return ($now >= $start_date && $now <= $end_date);
    }

    public function getCMSFields()
    {
        $f = parent::getCMSFields();

        $f->addFieldToTab('Root.Main', $date = new DatetimeField('SubmissionBeginDate', 'Submission Begin Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Main', $date = new DatetimeField('SubmissionEndDate', 'Submission End Date'));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');

        $f->addFieldsToTab('Root.Main', new NumericField('MaxSubmissionAllowedPerUser', 'Max. Submission Allowed Per User'));

        if($this->ID > 0) {
            $config = new GridFieldConfig_RelationEditor(100);
            $config->removeComponentsByType('GridFieldEditButton');
            $config->removeComponentsByType('GridFieldAddNewButton');
            $config->addComponent(new GridFieldDeleteAction('unlinkrelation'));
            $groups = new GridField('AllowedGroups', 'Allowed Groups', $this->AllowedGroups(), $config);
            $f->addFieldToTab('Root.Main', $groups);
        }

        return $f;
    }

}