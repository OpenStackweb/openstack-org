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


final class SummitAdminUI extends DataExtension
{
    private static $better_buttons_actions = array
    (
        'forcephase',
        'setasactive',
        'resetvotes',
        'handlevotinglists'
    );

    /**
     * @var array
     */
    private static $summary_fields = array
    (
        'Title'  => 'Title',
        'Status' => 'Status',
    );

    public function updateCMSFields(FieldList $f) {
        //clear all fields
        $oldFields = $f->toArray();
        foreach($oldFields as $field){
            $f->remove($field);
        }

        $_REQUEST['SummitID'] = $this->owner->ID;

        $f->add($rootTab = new TabSet("Root", $tabMain = new Tab('Main')));

        $summit_time_zone = null;
        if($this->owner->TimeZone) {
            $time_zone_list = timezone_identifiers_list();
            $summit_time_zone = $time_zone_list[$this->owner->TimeZone];
        }

        if ($this->owner->RandomVotingLists()->exists()) {
            $f->addFieldToTab('Root.Main',
                HeaderField::create('The presentations in this summit have been randomised for voting', 4));
        }

        $f->addFieldToTab('Root.Main', new TextField('Title', 'Title'));
        $f->addFieldToTab('Root.Main', $link = new TextField('Link', 'Summit Page Link'));

        $link->setDescription('The link to the site page for this summit. Eg: <em>/summit/vancouver-2015/</em>');
        $f->addFieldToTab('Root.Main', new CheckboxField('Active', 'This is the active summit'));
        $f->addFieldToTab('Root.Main', $date_label = new TextField('DateLabel', 'Date label'));
        $date_label->setDescription('A readable piece of text representing the date, e.g. <em>May 12-20, 2015</em> or <em>December 2016</em>');

        $f->addFieldToTab('Root.Main', $registration_link = new TextField('RegistrationLink', 'Registration Link'));
        $registration_link->setDescription('Link to the site where tickets can be purchased.');

        $f->addFieldsToTab('Root.Dates',
            $ddl_timezone = new DropdownField('TimeZone', 'Time Zone', DateTimeZone::listIdentifiers()));
        $ddl_timezone->setEmptyString('-- Select a Timezone --');

        if($summit_time_zone) {
            $f->addFieldToTab('Root.Dates', new HeaderField("All dates below are in <span style='color:red;'>$summit_time_zone</span> time."));
        }
        else {
            $f->addFieldToTab('Root.Dates', new HeaderField("All dates below in the timezone of the summit's venue."));
        }

        $f->addFieldToTab('Root.Dates', $date = new DatetimeField('SummitBeginDate', "When does the summit begin?"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Dates', $date = new DatetimeField('SummitEndDate', "When does the summit end?"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Dates', $date = new DatetimeField('StartShowingVenuesDate', "When do you begin showing venues?"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');

        $f->addFieldToTab('Root.Dates', $date = new DatetimeField('SubmissionBeginDate', "When do submissions begin?"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Dates', $date = new DatetimeField('SubmissionEndDate', "When do submissions end?"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');

        $f->addFieldToTab('Root.Dates', $date = new DatetimeField('VotingBeginDate', "When does voting begin?"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Dates', $date = new DatetimeField('VotingEndDate', "When does voting end?"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Dates', $date = new DatetimeField('SelectionBeginDate', "When do selections begin?"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Dates', $date = new DatetimeField('SelectionEndDate', "When do selections end?"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Dates', $date = new DatetimeField('RegistrationBeginDate', "When does registration begin?"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Dates', $date = new DatetimeField('RegistrationEndDate', "When does registration end?"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');
        $f->addFieldToTab('Root.Dates', $date = new DateField('ScheduleDefaultStartDate', "Default Start Date to show on schedule page?"));
        $date->setConfig('showcalendar', true);
        $date->setConfig('dateformat', 'dd/MM/yyyy');

        $f->addFieldsToTab('Root.Main', new NumericField('MaxSubmissionAllowedPerUser', 'Max. Submission Allowed Per User'));

        $logo_field = new UploadField('Logo', 'Logo');
        $logo_field->setAllowedMaxFileNumber(1);
        $logo_field->setAllowedFileCategories('image');
        $logo_field->setFolderName('summits/logos/');
        $logo_field->getValidator()->setAllowedMaxFileSize(1024*1024*1);
        $f->addFieldToTab('Root.Main', $logo_field);

        $f->addFieldToTab('Root.Main', new TextField('ComingSoonBtnText', 'Coming Soon Btn Text'));
        $f->addFieldToTab('Root.Main', new TextField('ExternalEventId', 'Eventbrite Event Id'));

        if ($this->owner->ID > 0) {
            $summit_id = $this->owner->ID;
            // tracks
            $config = GridFieldConfig_RecordEditor::create(25);
            $categories = new GridField('Categories', 'Presentation Categories', $this->owner->getCategories(), $config);
            $f->addFieldToTab('Root.Presentation Categories', $categories);

            $config = GridFieldConfig_RelationEditor::create(25);
            $config->removeComponentsByType(new GridFieldDataColumns());
            $config->removeComponentsByType(new GridFieldDetailForm());
            $config->addComponent(new GridFieldUpdateDefaultCategoryTags);
            $default_tags = new GridField('CategoryDefaultTags', 'Category Default Tags', $this->owner->CategoryDefaultTags(), $config);
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
            $f->addFieldToTab('Root.Presentation Categories', $default_tags);

            // track groups
            $config = GridFieldConfig_RecordEditor::create(25);
            $config->removeComponentsByType('GridFieldAddNewButton');
            $multi_class_selector = new GridFieldAddNewMultiClass();
            $multi_class_selector->setClasses
            (
                array
                (
                    'PresentationCategoryGroup'        => 'Category Group',
                    'PrivatePresentationCategoryGroup' => 'Private Category Group',
                )
            );
            $config->addComponent($multi_class_selector);
            $categories = new GridField('CategoryGroups', 'Category Groups', $this->owner->CategoryGroups(), $config);
            $f->addFieldToTab('Root.Category Groups', $categories);

            // locations
            $config = GridFieldConfig_RecordEditor::create();
            $config->removeComponentsByType('GridFieldAddNewButton');
            $multi_class_selector = new GridFieldAddNewMultiClass();
            $multi_class_selector->setClasses
            (
                array
                (
                    'SummitVenue' => 'Venue',
                    'SummitHotel' => 'Hotel',
                    'SummitAirport' => 'Airport',
                    'SummitExternalLocation' => 'External Location',
                )
            );
            $config->addComponent($multi_class_selector);
            $config->addComponent($sort = new GridFieldSortableRows('Order'));
            $gridField = new GridField('Locations', 'Locations',
                $this->owner->Locations()->where("ClassName <> 'SummitVenueRoom' "), $config);
            $f->addFieldToTab('Root.Locations', $gridField);

            // types

            $config = GridFieldConfig_RecordEditor::create();
            $config->addComponent(new GridFieldAddDefaultSummitTypes);
            $gridField = new GridField('SummitTypes', 'SummitTypes', $this->owner->Types(), $config);
            $f->addFieldToTab('Root.SummitTypes', $gridField);

            // event types
            $config = GridFieldConfig_RecordEditor::create();
            $config->addComponent(new GridFieldAddDefaultEventTypes);
            $gridField = new GridField('EventTypes', 'EventTypes', $this->owner->EventTypes(), $config);
            $f->addFieldToTab('Root.EventTypes', $gridField);

            //schedule

            $config = GridFieldConfig_RecordEditor::create(25);
            $config->addComponent(new GridFieldAjaxRefresh(1000, false));
            $config->removeComponentsByType('GridFieldDeleteAction');
            $gridField = new GridField('Schedule', 'Schedule', $this->owner->Events()->filter('Published', true)->sort
            (
                array
                (
                    'StartDate' => 'ASC',
                    'EndDate' => 'ASC'
                )
            ), $config);
            $config->getComponentByType("GridFieldDataColumns")->setFieldCasting(array("Description" => "HTMLText->BigSummary"));
            $f->addFieldToTab('Root.Schedule', $gridField);
            $config->addComponent(new GridFieldPublishSummitEventAction);

            // events

            $config = GridFieldConfig_RecordEditor::create(25);
            $config->addComponent(new GridFieldPublishSummitEventAction);
            $config->addComponent(new GridFieldAjaxRefresh(1000, false));
            $config->addComponent($bulk_summit_types = new GridFieldBulkActionAssignSummitTypeSummitEvents);
            $bulk_summit_types->setTitle('Set Summit Type');
            $gridField = new GridField('Events', 'Events', $this->owner->Events()->filter('ClassName', 'SummitEvent'),
                $config);
            $config->getComponentByType("GridFieldDataColumns")->setFieldCasting(array("Description" => "HTMLText->BigSummary"));
            $f->addFieldToTab('Root.Events', $gridField);

            //track selection list presentations

            $config = GridFieldConfig_RecordEditor::create(25);
            $gridField = new GridField('TrackChairsSelectionLists', 'TrackChairs Selection Lists',
                SummitSelectedPresentationList::get()->filter('ListType', 'Group')
                    ->where(' CategoryID IN ( SELECT ID FROM PresentationCategory WHERE SummitID = ' . $summit_id . ')')
                , $config);
            $f->addFieldToTab('Root.TrackChairs Selection Lists', $gridField);


            // attendees

            $config = GridFieldConfig_RecordEditor::create(25);
            $gridField = new GridField('Attendees', 'Attendees', $this->owner->Attendees(), $config);
            $f->addFieldToTab('Root.Attendees', $gridField);

            //tickets types

            $config = GridFieldConfig_RecordEditor::create(25);
            $gridField = new GridField('SummitTicketTypes', 'Ticket Types', $this->owner->SummitTicketTypes(), $config);
            $f->addFieldToTab('Root.TicketTypes', $gridField);

            // promo codes

            $config = GridFieldConfig_RecordEditor::create(50);
            $config->removeComponentsByType('GridFieldAddNewButton');
            $multi_class_selector = new GridFieldAddNewMultiClass();


            $multi_class_selector->setClasses
            (
                array
                (
                    'SpeakerSummitRegistrationPromoCode' => 'Speaker Promo Code',
                )
            );

            $config->addComponent($multi_class_selector);

            $promo_codes = new GridField('SummitRegistrationPromoCodes', 'Registration Promo Codes',
                $this->owner->SummitRegistrationPromoCodes(), $config);
            $f->addFieldToTab('Root.RegistrationPromoCodes', $promo_codes);

            // speakers

            $config = GridFieldConfig_RecordEditor::create(25);
            $gridField = new GridField('Speakers', 'Speakers', $this->owner->Speakers(false), $config);
            $config->getComponentByType("GridFieldDataColumns")->setFieldCasting(array("Bio" => "HTMLText->BigSummary"));
            $f->addFieldToTab('Root.Speakers', $gridField);

            // presentations

            $config = GridFieldConfig_RecordEditor::create(25);
            $config->addComponent(new GridFieldPublishSummitEventAction);
            $config->addComponent(new GridFieldAjaxRefresh(1000, false));
            $config->addComponent($bulk_summit_types = new GridFieldBulkActionAssignSummitTypeSummitEvents);
            $bulk_summit_types->setTitle('Set Summit Type');
            $gridField = new GridField('Presentations', 'Presentations',
                $this->owner->Presentations()->where(" Title IS NOT NULL AND Title <>'' "), $config);
            $config->getComponentByType("GridFieldDataColumns")->setFieldCasting(array("Description" => "HTMLText->BigSummary"));
            $f->addFieldToTab('Root.Presentations', $gridField);

            // push notifications
            $config = GridFieldConfig_RecordEditor::create(25);
            $config->addComponent(new GridFieldAjaxRefresh(1000, false));
            $config->getComponentByType('GridFieldDataColumns')->setDisplayFields
            (
                array(
                    'Channel'        => 'Channel',
                    'Message'        => 'Message',
                    'Owner.FullName' => 'Owner',
                    'IsSent'         => 'Is Sent?',
                    'SentDate'       => 'Sent Date',
                )
            );
            $config->getComponentByType('GridFieldDetailForm')->setItemRequestClass('GridFieldDetailFormPushNotification');

            $gridField = new GridField('Notifications', 'Notifications', $this->owner->Notifications(), $config);


            $f->addFieldToTab('Root.PushNotifications', $gridField);

            //entity events

            $config = GridFieldConfig_RecordEditor::create(25);
            $config->addComponent(new GridFieldAjaxRefresh(1000, false));
            $config->addComponent(new GridFieldWipeDevicesDataAction);
            $config->addComponent(new GridFieldDeleteAllSummitEntityEventsAction);
            $config->removeComponentsByType('GridFieldAddNewButton');
            $gridField = new GridField('EntityEvents', 'EntityEvents', $this->owner->EntityEvents(), $config);
            $f->addFieldToTab('Root.EntityEvents', $gridField);

            //TrackChairs
            $config = GridFieldConfig_RecordEditor::create(25);
            $config->addComponent(new GridFieldAjaxRefresh(1000, false));
            $gridField = new GridField('TrackChairs', 'TrackChairs', $this->owner->TrackChairs(), $config);
            $f->addFieldToTab('Root.TrackChairs', $gridField);

            //RSVP templates

            $config = GridFieldConfig_RecordEditor::create(40);
            $config->addComponent(new GridFieldAjaxRefresh(1000, false));
            $gridField = new GridField('RSVPTemplates', 'RSVPTemplates', $this->owner->RSVPTemplates(), $config);
            $f->addFieldToTab('Root.RSVPTemplates', $gridField);
        }
    }


    public function getBetterButtonsActions()
    {
        $extension        = $this->owner->getExtensionInstance("BetterButtonDataObject");
        if(is_null($extension)) return;
        $extension->owner = $this->owner;
        $f                = $extension->getBetterButtonsActions();
        if (Director::isDev() && Permission::check('ADMIN')) {
            $f->push(new DropdownFormAction('Dev tools', [
                new BetterButtonNestedForm('forcephase', 'Force phase...', FieldList::create(
                    DropdownField::create('Phase', 'Choose a phase', [
                        0 => 'ACCEPTING SUBMISSIONS',
                        1 => 'COMMUNITY VOTING',
                        2 => 'TRACK CHAIR SELECTION',
                        3 => 'REGISTRATION',
                        4 => 'SUMMIT IS ON',
                    ])
                )),
                BetterButtonCustomAction::create('resetvotes', 'Reset presentation votes')
                    ->setRedirectType(BetterButtonCustomAction::REFRESH),
                BetterButtonCustomAction::create('setasactive', 'Set as active')
                    ->setRedirectType(BetterButtonCustomAction::REFRESH)
            ]));
        }

        $text = $this->owner->RandomVotingLists()->exists() ? "Regenerate random voting order" : "Generate random voting order";
        $f->push($random = BetterButtonCustomAction::create(
            'handlevotinglists',
            $text
        )
            ->setRedirectType(BetterButtonCustomAction::REFRESH)
        );
        if (!$this->owner->checkRange("Voting")) {
            $random->setConfirmation('You are randomising the presentations outside of the voting phase. If there are more presentations coming, this could cause errors. Are you sure you want to do this?');
        }
        return $f;
    }

    public function forcephase($data, $form)
    {
        $span = 10;
        $subtractor = (($data['Phase'] * $span) * -1);
        foreach (['Submission', 'Voting', 'Selection', 'Registration'] as $period) {
            $date = (new DateTime(null, new DateTimeZone('UTC')))->modify("$subtractor days");
            $this->owner->{"set" . $period . "BeginDate"}($date->format("Y-m-d"));
            $subtractor += $span;
            $date->add(DateInterval::createFromDateString("$span days"));
            $this->owner->{"set" . $period . "EndDate"}($date->format("Y-m-d"));
        }

        $this->owner->write();
        $form->sessionMessage('Phase updated', 'good');
    }

    public function resetvotes()
    {
        DB::query(sprintf(
            "DELETE FROM PresentationVote WHERE PresentationID IN (%s)",
            implode(',', $this->owner->Presentations()->column('ID'))
        ));

        return 'All votes have been reset';
    }

    public function setasactive()
    {
        DB::query("UPDATE Summit SET Active = 0");
        $this->owner->Active = 1;
        $this->owner->write();

        return 'Summit is now active';
    }

    public function handlevotinglists()
    {
        $this->owner->generateVotingLists();

        return Summit::config()->random_list_count . " random incarnations created";
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