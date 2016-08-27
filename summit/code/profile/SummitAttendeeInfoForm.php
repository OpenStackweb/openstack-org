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
final class SummitAttendeeInfoForm extends BootstrapForm
{
    function __construct($controller, $name)
    {
        $fields = new FieldList
        (
            array
            (
                $t1 = new TextField('ExternalOrderId', 'Eventbrite Order #'),
            )
        );

        $t1->setAttribute('placeholder', 'Enter your Eventbrite order #');
        $t1->addExtraClass('event-brite-order-number');
        $actions   = new FieldList();
        $validator = null;

        if(count(Session::get('attendees')) > 0) // we have and order in process .. then select the attenders
        {
            $t1->setValue(Session::get('ExternalOrderId'));
            $t1->setReadonly(true);

            $fields->add(new LiteralField('ctrl1','Current Order has following registered attendees, please select one:'));
            $options = array();
            foreach(Session::get('attendees') as $attendee)
            {
                $ticket_external_id = intval($attendee['ticket_class_id']);
                $ticket_type = SummitTicketType::get()->filter('ExternalId', $ticket_external_id)->first();
                if(is_null($ticket_type)) continue;
                $options[$attendee['id']] = $attendee['profile']['name'].' ('.$ticket_type->Name.')';
            }
            $attendees_ctrl = new OptionSetField('SelectedAttendee','', $options);
            $fields->add($attendees_ctrl);

            $validator = new RequiredFields(array('ExternalOrderId'));

            // Create action
            $actions = new FieldList
            (
                $btn_clear = new FormAction('clearSummitAttendeeInfo', 'Clear'),
                $btn       = new FormAction('saveSummitAttendeeInfo', 'Done')
            );

            $btn->addExtraClass('btn btn-default active');
            $btn_clear->addExtraClass('btn btn-danger active');
        }

        if(!Member::currentUser()->getCurrentSummitAttendee()){
            $validator = new RequiredFields(array('ExternalOrderId'));
            // Create action
            $actions = new FieldList
            (
                $btn = new FormAction('saveSummitAttendeeInfo', 'Get Order')
            );
            $btn->addExtraClass('btn btn-default active');
        }

        parent::__construct($controller, $name, $fields, $actions, $validator);

    }

    public function loadDataFrom($data, $mergeStrategy = 0, $fieldList = null)
    {
        parent::loadDataFrom($data, $mergeStrategy, $fieldList);

        if($data && $data instanceof SummitAttendee && $data->ID > 0)
        {
            // if we have an attendee then show the form on readonly mode ...
            $ticket = $data->Tickets()->first();
            $this->fields->insertAfter($t1 = new TextField('TicketBoughtDate', 'Ticket Bought Date', $ticket->TicketBoughtDate),'ExternalOrderId');
            $t2 = $this->fields->fieldByName('ExternalOrderId');
            $t2->setValue($ticket->ExternalOrderId);
            $this->fields->insertAfter($t3 = new TextField('TicketType', 'Ticket Type', $ticket->TicketType()->Name), 'TicketBoughtDate');
            $t1->setReadonly(true);
            $t2->setReadonly(true);
            $t3->setReadonly(true);
        }

    }
}