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
class EditProfilePageSummitAttendeeExtension extends Extension
{

    /**
     * @var IEventbriteEventManager
     */
    private $manager;

    public function getEventbriteEventManager()
    {
        return $this->manager;
    }

    public function setEventbriteEventManager(IEventbriteEventManager $manager)
    {
        $this->manager = $manager;
    }

    public function onBeforeInit()
    {
        Config::inst()->update(get_class($this), 'allowed_actions', array
        (
            'attendeeInfoRegistration',
            'SummitAttendeeInfoForm',
            'saveSummitAttendeeInfo',
            'clearSummitAttendeeInfo',
        ));
    }

    public function getNavActionsExtensions(&$html)
    {
        $view = new SSViewer('EditProfilePage_SummitAttendeeNav');
        $html .= $view->process($this->owner);
    }

    public function getNavMessageExtensions(&$html){
        $view = new SSViewer('EditProfilePage_SummitAttendeeMessage');
        $html .= $view->process($this->owner);
    }

    public function ActiveSummit()
    {
        return Summit::ActiveSummit();
    }

    public function attendeeInfoRegistration(SS_HTTPRequest $request)
    {
        //return $this->owner->customise(array())->renderWith(array('EditProfilePage_attendeeInfoRegistration', 'Page'));
        return $this->owner->getViewer('attendeeInfoRegistration')->process($this->owner);
    }

    public function SummitAttendeeInfoForm()
    {
        if ($current_member = Member::currentUser())
        {
            $form = new SummitAttendeeInfoForm($this->owner, 'SummitAttendeeInfoForm');
            //Populate the form with the current members data
            $attendee = $current_member->getCurrentSummitAttendee();
            if($attendee) $form->loadDataFrom($attendee->data());
            return $form;
        }
    }

    public function saveSummitAttendeeInfo($data, Form $form)
    {
        if ($current_member = Member::currentUser())
        {
            $attendee = $current_member->getCurrentSummitAttendee();
            if(!$attendee && !isset($data['SelectedAttendee']))
            {

                try
                {
                    if(Session::get('attendees'))
                    {
                        // already retrieved data
                        $form->sessionMessage('Please select an attendee', "bad");
                        return $this->owner->redirect($this->owner->Link('attendeeInfoRegistration'));
                    }
                    $attendees = $this->manager->getOrderAttendees($data['ExternalOrderId']);
                    Session::set('attendees', $attendees);
                    Session::set('ExternalOrderId', $data['ExternalOrderId']);
                    if(isset($data['SharedContactInfo']))
                        Session::set('SharedContactInfo',$data['SharedContactInfo']);
                    return $this->owner->redirect($this->owner->Link('attendeeInfoRegistration'));
                }
                catch(InvalidEventbriteOrderStatusException $ex1)
                {
                    Session::clear('attendees');
                    Session::clear('ExternalOrderId');
                    Session::clear('SharedContactInfo');
                    $form->sessionMessage('Current order was cancelled, please try with another one!', "bad");
                    return $this->owner->redirect($this->owner->Link('attendeeInfoRegistration'));
                }
            }
            if($attendee)
            {
                return $this->owner->redirect($this->owner->Link('attendeeInfoRegistration'));
            }
            if(isset($data['SelectedAttendee']))
            {
                try {
                    // register attendee with current member
                    $attendees                = Session::get('attendees');
                    $external_order_id        = Session::get('ExternalOrderId');
                    $external_attendee_id     = $data['SelectedAttendee'];
                    $selected_attendee_data   = $attendees[$external_attendee_id];
                    $external_event_id        = $selected_attendee_data['event_id'];
                    $external_ticket_class_id = $selected_attendee_data['ticket_class_id'];
                    $created                  = $selected_attendee_data['created'];

                    $this->manager->registerAttendee
                    (
                        $current_member,
                        $external_event_id,
                        $external_order_id,
                        $external_attendee_id,
                        $external_ticket_class_id,
                        $created,
                        isset($data['SharedContactInfo']) ? $data['SharedContactInfo'] : false
                    );
                    Session::clear('attendees');
                    Session::clear('ExternalOrderId');
                    Session::clear('SharedContactInfo');
                    $form->sessionMessage('Your registration request was successfully processed!', "good");
                    return $this->owner->redirect($this->owner->Link('attendeeInfoRegistration'));
                }
                catch(Exception $ex)
                {
                    Session::clear('attendees');
                    Session::clear('ExternalOrderId');
                    Session::clear('SharedContactInfo');
                    SS_Log::log($ex->getMessage(), SS_Log::ERR);
                    $form->sessionMessage('Your request can not be processed, please contact your administrator', "bad");
                    return $this->owner->redirect($this->owner->Link('attendeeInfoRegistration'));
                }
            }

        }
        return $this->owner->httpError(403);
    }

    public function clearSummitAttendeeInfo($data, $form)
    {
        Session::clear('attendees');
        Session::clear('ExternalOrderId');
        Session::clear('SharedContactInfo');
        return $this->owner->redirect($this->owner->Link('attendeeInfoRegistration'));
    }
}