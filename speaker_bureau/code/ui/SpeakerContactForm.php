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

/**
 * Class SpeakerContactForm
 */
final class SpeakerContactForm extends BootstrapForm
{

    function __construct($controller, $name, $speakerID, $use_actions = true)
    {

        $fields = new FieldList;
        //point of contact
        $speakerIDfield = new HiddenField('speaker_id');
        $speakerIDfield->setValue($speakerID);
        $fields->push($speakerIDfield);
        $fields->push(new TextField('org_name', 'Name of Organizer'));
        $fields->push(new EmailField('org_email', 'Email'));
        $fields->push(new TextField('event_name', 'Event'));
        $fields->push(new TextField('event_format', 'Format/Length'));
        $fields->push(new TextField('event_attendance', 'Expected Attendace (number)'));
        $fields->push(new TextField('event_date', 'Date of Event'));
        $fields->push(new TextField('event_location', 'Location'));
        $fields->push(new TextField('event_topic', 'Topic(s)'));
        $request = new HtmlEditorField('general_request', 'General Request');
        $request->setRows(10);
        $fields->push($request);

        $sec_field = new TextField('field_98438688', 'field_98438688');
        $sec_field->addExtraClass('honey');
        $fields->push($sec_field);

        // Create action
        $actions = new FieldList();
        if ($use_actions) {
            $actions->push(new FormAction('sendSpeakerEmail', 'Send'));
        }

        parent::__construct($controller, $name, $fields, $actions);
    }

    function forTemplate()
    {
        return $this->renderWith(array(
            $this->class,
            'Form'
        ));
    }

    function submit($data, $form)
    {
        // do stuff here
    }
}