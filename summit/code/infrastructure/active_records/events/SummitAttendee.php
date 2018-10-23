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
final class SummitAttendee extends DataObject implements ISummitAttendee
{

    private static $db = array
    (
        'SharedContactInfo'       => 'Boolean',
        'SummitHallCheckedIn'     => 'Boolean',
        'SummitHallCheckedInDate' => 'SS_Datetime',
    );

    private static $has_many = array
    (
        'Tickets' => 'SummitAttendeeTicket',
    );

    private static $defaults = array
    (
    );

    private static $many_many = array
    (

    );

    private static $belongs_to = array
    (

    );

    private static $has_one = array
    (
        'Member'     => 'Member',
        'Summit'     => 'Summit',
    );

    private static $summary_fields = [

        "Member.Email"        => 'Member',
        'SummitHallCheckedIn' => "Is Checked In",
        'TicketsCount'        => '# Tickets'
    ];

    static $indexes = array
    (
        //'Summit_Member' =>  array('type'=>'unique', 'value'=>'SummitID,MemberID'),
    );

    private static $searchable_fields = array
    (
    );

    protected function onBeforeDelete()
    {
        parent::onBeforeDelete();
        foreach($this->Tickets() as $t)
            $t->delete();
    }

    public function TicketsCount()
    {
        return (int)$this->Tickets()->count();
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    /**
     * @return ICommunityMember
     */
    public function getMember()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Member')->getTarget();
    }

    /**
     * @return string
     */
    public function getMemberFullName(){
        return $this->Member()->exists() ? $this->Member()->getFullName() : '';
    }
    /**
     * @return ISummit
     */
    public function getSummit()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Summit')->getTarget();
    }

    /**
     * @return DateTime
     */
    public function getTicketBoughtDate()
    {
        return $this->getField('TicketBoughtDate');
    }

    /**
     * @return bool
     */
    public function allowSharedContactInfo()
    {
        return $this->getField('SharedContactInfo');
    }

    /**
     * @return bool
     */
    public function isSummitHallCheckedIn()
    {
        return $this->getField('SummitHallCheckedIn');
    }

    /**
     * @return void
     */
    public function registerSummitHallChecking()
    {
       if($this->SummitHallCheckedIn) return;

       $this->SummitHallCheckedIn      = true;
       $this->SummitHallCheckedInDate =  CustomMySQLDatabase::nowRfc2822();
    }

    public function getCMSFields()
    {

        $f = new FieldList
        (
            $rootTab = new TabSet("Root", $tabMain = new Tab('Main'))
        );

        $f->addFieldToTab('Root.Main',  new DropdownField('SummitID','Summit', Summit::get()->map('ID', 'Title')));
        $f->addFieldsToTab('Root.Main', new CheckboxField('SharedContactInfo', 'Allow Shared Contact Info?'));
        $f->addFieldsToTab('Root.Main', new CheckboxField('SummitHallCheckedIn', 'Is SummitHall checked In?'));
        $f->addFieldsToTab('Root.Main', $checked_in_date = new DatetimeField('SummitHallCheckedInDate', 'SummitHall checked In Date'));
        $checked_in_date->getDateField()->setConfig('showcalendar', true);
        $f->addFieldsToTab('Root.Main', new MemberAutoCompleteField('Member', 'Member'));

        if($this->ID > 0)
        {
            //tickets

            $config = GridFieldConfig_RecordEditor::create(10);
            $gridField = new GridField('Tickets', 'Tickets', $this->Tickets(), $config);
            $f->addFieldToTab('Root.Tickets', $gridField);
        }
        return $f;
    }

    protected function validate()
    {

        $valid = parent::validate();
        if (!$valid->valid()) {
            return $valid;
        }
        if (intval($this->MemberID) == 0) {
            return $valid->error('Member is required!');
        }
        if (intval($this->SummitID) == 0) {
            return $valid->error('Summit is required!');
        }
        $old_ones = intval(SummitAttendee::get()->filter(array('MemberID' => $this->MemberID , 'SummitID' => $this->SummitID))->where(" ID <> {$this->ID} ")->count());
        if($old_ones > 0){
            return $valid->error(sprintf('There is already an attendee for member %s on summit %s!', $this->MemberID, $this->SummitID));
        }
        return $valid;
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


    /**
     * @param ISummitAttendeeTicket $ticket
     * @return bool
     */
    public function hasTicket(ISummitAttendeeTicket $ticket)
    {
        $query   = new QueryObject($this);
        $tickets = AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Tickets', $query)->toArray();
        foreach($tickets as $t)
        {
            if(
                $t->ExternalOrderId === $ticket->ExternalOrderId &&
                $t->ExternalAttendeeId === $ticket->ExternalAttendeeId &&
                $t->TicketType()->exists() &&
                $ticket->TicketType()->exists() &&
                $t->TicketType()->ExternalId === $ticket->TicketType()->ExternalId
            )
            return true;
        }
        return false;
    }

    /**
     * @return ISummitAttendeeTicket[]
     * @throws Exception
     */
    public function getTickets()
    {
        $query   = new QueryObject($this);
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Tickets', $query)->toArray();
    }

    /**
     * @param ISummitAttendeeTicket $ticket
     * @return $this
     */
    public function addTicket(ISummitAttendeeTicket $ticket)
    {
        $query = new QueryObject($this);
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Tickets', $query)->add($ticket);
        return $this;
    }

    /**
     * @param bool $must_share
     * @return $this
     */
    public function setShareContactInfo($must_share)
    {
        $this->setField('SharedContactInfo', $must_share);
        return $this;
    }


    /**
     * @return string
     * @throws Exception
     */
    public function getTicketIDs()
    {
        $ids = $this->Tickets()->column('ExternalOrderId');
        return (count($ids)) ? implode(', ',$ids) : '';
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getBoughtDate($format = 'h:ia, M j, Y ')
    {
        $last_ticket = $this->Tickets()->sort('TicketBoughtDate','DESC')->first();
        if ($last_ticket) {
            $bought_date = new DateTime($last_ticket->TicketBoughtDate);
            return $bought_date->format($format);
        }

        return '';

    }
}