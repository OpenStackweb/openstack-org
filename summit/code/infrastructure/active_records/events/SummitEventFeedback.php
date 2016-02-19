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
    class SummitEventFeedback extends DataObject implements ISummitEventFeedBack
{
    private static $db = array
    (
        'Rate'         => 'Float',
        'Note'         => 'HTMLText',
        'Approved'     => 'Boolean',
        'ApprovedDate' => 'SS_DateTime',
    );

    private static $has_many = array
    (
    );

    private static $defaults = array
    (
        'Approved' => false
    );

    private static $has_one = array
    (
        'Owner'      => 'Member',
        'ApprovedBy' => 'Member',
        'Event'      => 'SummitEvent',
    );

    private static $summary_fields = array
    (
        'Rate',
        'Owner.Email',
        'Approved',
        'ApprovedDate',
    );

    private static $searchable_fields = array
    (
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    /**
     * @return int
     */
    public function getRate()
    {
        return (float)$this->getField('Rate');
    }

    /**
     * @return int
     */
    public function getRateAsWidth()
    {
        return (float)$this->getField('Rate')*20;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->getField('Note');
    }

    public function getDateNice() {
        $timestamp = strtotime($this->Created);
        $diff = time() - $timestamp;

        if ($diff < 60 ) {
            $date_nice = $diff.' seconds ago';
        } else if ($diff < (60 *60)) {
            $int_diff = floor($diff/60);
            $plural = ($int_diff > 1) ? 's' : '';
            $date_nice = $int_diff.' minute'.$plural.' ago';
        } else if ($diff < (60 * 60 * 24)) {
            $int_diff = floor($diff/(60*60));
            $plural = ($int_diff > 1) ? 's' : '';
            $date_nice = $int_diff.' hour'.$plural.' ago';
        } else {
            $int_diff = floor($diff/(60*60*24));
            $plural = ($int_diff > 1) ? 's' : '';
            $date_nice = $int_diff.' day'.$plural.' ago';
        }

        return $date_nice;

    }

    /**
     * @return ICommunityMember
     */
    public function getOwner()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Owner')->getTarget();
    }

    /**
     * @return ISummitEvent
     */
    public function getEvent()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Event')->getTarget();
    }

    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $approved_date = $this->ApprovedDate;
        //first time published ...
        if($this->isApproved() && is_null($approved_date))
        {
            $this->Approved = false;
            $this->approve();
        }
    }

    /**
     * @return bool
     */
    public function isApproved()
    {
        return  $this->Approved;
    }

    public function approve()
    {
        if($this->Approved)
            throw new Exception('Already approved feedback Summit Event');

        $this->Approved = true;
        $this->ApprovedDate = MySQLDatabase56::nowRfc2822();
        $this->ApprovedByID = Member::currentUserID();
    }

    public function getCMSFields()
    {

        $summit_id = isset($_REQUEST['SummitID']) ? $_REQUEST['SummitID'] : $this->SummitID;

        $f = new FieldList();

        $f->add(new NumericField('Rate', 'Rate'));
        $f->add(new HtmlEditorField('Note', 'Note'));
        $f->add(new CheckboxField('Approved','Approved'));
        $f->add(new HiddenField('EventID', 'EventID'));
        $f->add(new HiddenField('OwnerID', 'OwnerID'));
        $f->add(new HiddenField('ApprovedByID', 'ApprovedByID'));


        return $f;
    }

    /**
     * @param Member $member
     * @return boolean
     */
    public function canView($member = null) {
        return Permission::check("ADMIN") || Permission::check("ADMIN_SUMMIT_APP") || Permission::check("ADMIN_SUMMIT_APP_SCHEDULE");
    }

    /**
     * @param Member $member
     * @return boolean
     */
    public function canEdit($member = null) {
        return Permission::check("ADMIN") || Permission::check("ADMIN_SUMMIT_APP") || Permission::check("ADMIN_SUMMIT_APP_SCHEDULE");
    }

}