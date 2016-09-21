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
final class SummitPushNotification extends CustomDataObject implements IEntity
{
    private static $db = array
    (
        'Message'  => 'Text',
        'Channel'  => "Enum('EVERYONE, SPEAKERS, ATTENDEES, MEMBERS, SUMMIT, EVENT, GROUP', 'EVERYONE')",
        'IsSent'   => 'Boolean',
        'SentDate' => 'SS_Datetime',
    );

    private static $summary_fields = array
    (
    );

    private static $has_one = array
    (
        'Summit'    => 'Summit',
        'Owner'     => 'Member',
        'Event'     => 'SummitEvent',
        'Group'     => 'Group',
    );

    private static $many_many = array
    (
        'Recipients'     => 'Member',
    );

    private static $indexes = array(

    );

    public function getCMSFields()
    {
        $summit_id = isset($_REQUEST['SummitID'])?$_REQUEST['SummitID'] : Summit::ActiveSummitID();
        Requirements::javascript('summit/javascript/SummitPushNotification.js');

        $f = new FieldList
        (
            $rootTab = new TabSet("Root", $tabMain = new Tab('Main'))
        );

        $f->addFieldToTab('Root.Main', $txt = new TextareaField('Message','Message'));
        $txt->setAttribute('required','true');
        $f->addFieldToTab('Root.Main', $ddl_channel = new DropdownField('Channel','Channel', singleton('SummitPushNotification')->dbObject('Channel')->enumValues()));

        $f->addFieldToTab('Root.Main',
            $ddl_events  = new DropdownField
            (
                'EventID',
                'Event',
                SummitEvent::get()->filter
                (
                    [
                        'Published' => 1,
                        'SummitID'  => $summit_id
                    ]
                )->sort('Title', 'ASC')->Map('ID','FormattedTitle'))
        );

        $f->addFieldToTab('Root.Main',
            $ddl_groups  = new DropdownField
            (
                'GroupID',
                'Group',
                Group::get()->sort('Title', 'ASC')->Map('ID','Title'))
        );

        $f->addFieldToTab('Root.Main', new HiddenField('SummitID','SummitID'));

        $ddl_channel->setEmptyString('--SELECT A CHANNEL--');
        $ddl_channel->setAttribute('required','true');

        $ddl_events->setEmptyString('--SELECT AN EVENT--');
        $ddl_events->addExtraClass('hidden');

        $ddl_groups->setEmptyString('--SELECT A GROUP--');
        $ddl_groups->addExtraClass('hidden');

        $config = GridFieldConfig_RelationEditor::create(50);
        $config->removeComponentsByType('GridFieldAddExistingAutocompleter');
        $config->removeComponentsByType('GridFieldAddNewButton');
        $config->addComponent($auto_completer = new CustomGridFieldAddExistingAutocompleter('buttons-before-right'));
        $auto_completer->setResultsFormat('$Title ($Email)');
        $recipients = new GridField('Recipients', 'Member Recipients', $this->Recipients(), $config);
        $f->addFieldToTab('Root.Main', $recipients);

        return $f;
    }


    public function getCMSActions(){
        $actions = parent::getCMSActions();
        return $actions;
    }

    public function sent()
    {
        if($this->isAlreadySent()) throw new EntityValidationException('Push notification already sent!.');
        $this->IsSent   = true;
        $this->SentDate = MySQLDatabase56::nowRfc2822();
    }

    public function isAlreadySent(){
        return $this->IsSent;
    }

    protected function onBeforeWrite() {
        parent::onBeforeWrite();
        if($this->getIdentifier() === 0)
            $this->OwnerID = Member::currentUserID();
    }

    protected function onAfterWrite()
    {
        parent::onAfterWrite();
        CustomUnsavedRelationList::clearSessionData('SummitPushNotification', 'Recipients', 'Member');
    }

    protected function validate()
    {
        $valid = parent::validate();
        if(!$valid->valid()) return $valid;
        if($this->Channel === 'NONE')
            return $valid->error('You must set a valid channel.');

        if(empty($this->Message))
            return $valid->error('You must set a Message.');

        if($this->Channel === 'MEMBERS' && $this->Recipients()->count() === 0)
            return $valid->error('You must set at least one recipient for MEMBERS channel.');

        if($this->Channel === 'EVENT' && $this->EventID == 0)
            return $valid->error('You must set at least one Published Event for EVENT channel.');

        if($this->Channel === 'GROUP' && $this->GroupID == 0)
            return $valid->error('You must set at least one group for GROUP channel.');

        return $valid;
    }

    public function canDelete($member=null) {
        if ($this->isAlreadySent())
        {
            return false;
        }
        return parent::canDelete($member);
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
        if ($this->isAlreadySent())
        {
            return false;
        }
        return Permission::check("ADMIN") || Permission::check("ADMIN_SUMMIT_APP") || Permission::check("ADMIN_SUMMIT_APP_SCHEDULE");
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return intval($this->getField('ID'));
    }
}