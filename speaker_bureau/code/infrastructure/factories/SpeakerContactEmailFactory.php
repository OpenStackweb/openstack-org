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
 * Class SpeakerContactEmailFactory
 */
final class SpeakerContactEmailFactory implements ISpeakerContactEmailFactory
{

    /**
     * @param array $data
     * @return ISpeakerContactEmail
     */
    public function buildSpeakerContactEmail(array $data, PresentationSpeaker $speaker)
    {
        $contact_email = new SpeakerContactEmail();
        $contact_email->RecipientID = $speaker->ID;
        $contact_email->OrgName = $data['org_name'];
        $contact_email->OrgEmail = $data['org_email'];
        $contact_email->EventName = $data['event_name'];
        $contact_email->Format = $data['event_format'];
        $contact_email->Attendance = $data['event_attendance'];
        $contact_email->DateOfEvent = $data['event_date'];
        $contact_email->Location = $data['event_location'];
        $contact_email->Topics = $data['event_topic'];
        $contact_email->GeneralRequest = $data['general_request'];
        $contact_email->EmailSent = false;

        return $contact_email;
    }

}