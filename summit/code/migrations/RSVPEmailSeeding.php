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
final class RSVPEmailSeeding extends AbstractDBMigrationTask
{
    protected $title = "RSVPEmailSeeding";

    protected $description = 'Create the RSVP email template for attendees';

    function doUp()
    {
        if(intval(PermamailTemplate::get()->filter('Identifier', SUMMIT_ATTENDEE_RSVP_EMAIL)->count()) === 0 )
        {
            $email_tpl             = PermamailTemplate::create();
            $email_tpl->Identifier = SUMMIT_ATTENDEE_RSVP_EMAIL;
            $email_tpl->Subject    = 'OpenStack Summit - Thank you for your RSVP';
            $email_tpl->From       = 'noreply@openstack.org';
            $email_tpl->Content    ='<p>Thank you for your RSVP to {$Event.Title} at {$Event.getDateNice} . For your convenience, we have added this to My Schedule within the Summit Management tool. Be sure to synch it to your calendar by going <a href="{$ScheduleURL}">here</a>.</p><p>Cheers,</p><p>OpenStack Summit Team</p>';
            $email_tpl->write();
        }
    }

    function doDown()
    {

    }
}
