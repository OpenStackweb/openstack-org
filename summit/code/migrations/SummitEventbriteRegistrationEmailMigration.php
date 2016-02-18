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
class SummitEventbriteRegistrationEmailMigration extends AbstractDBMigrationTask
{
    protected $title = "SummitEventbriteRegistrationEmailMigration";

    protected $description = "create the default email templates for summit registration";

    function doUp()
    {
        global $database;

        if(intval(PermamailTemplate::get()->filter('Identifier', SUMMIT_ATTENDEE_CREATED_EMAIL_TEMPLATE)->count()) === 0 )
        {
            $email_tpl             = PermamailTemplate::create();
            $email_tpl->Identifier = SUMMIT_ATTENDEE_CREATED_EMAIL_TEMPLATE;
            $email_tpl->Subject    = 'Thank you for registering or the OpenStack Summit';
            $email_tpl->From       = 'noreply@openstack.org';
            $email_tpl->Content    ='<p>{$Attendee.Member.FirstName} {$Attendee.Member.Surname},</p><p>Thank you for registering for the OpenStack Summit {$Summit.Title}. Your EventBrite registration has now been associated with your OpenStack Foundation Account. We look forward to seeing you in {$Summit.Month}.</p><p>Thank you,</p><p>OpenStack Summit Team</p>';
            $email_tpl->write();
        }

        if(intval(PermamailTemplate::get()->filter('Identifier', SUMMIT_ATTENDEE_CREATE_MEMBERSHIP_INVITATION_EMAIL_TEMPLATE)->count()) === 0 )
        {
            $email_tpl             = PermamailTemplate::create();
            $email_tpl->Identifier = SUMMIT_ATTENDEE_CREATE_MEMBERSHIP_INVITATION_EMAIL_TEMPLATE;
            $email_tpl->Subject    = 'Thank you for registering or the OpenStack Summit';
            $email_tpl->From       = 'noreply@openstack.org';
            $email_tpl->Content    ='<p>Hello,</p><p>Thank you for registering for the OpenStack Summit {$Summit.Title}! We see you do not currently have an OpenStack Community Account or OpenStack Foundation Membership. In order to make the most of your visit, please proceed to https://www.openstack.org/join/register/ to complete your registration on openstack.org. Without this information, you will not have access to the Summit mobile apps.</p><p>Thank you,</p><p>OpenStack Summit Team</p>';
            $email_tpl->write();
        }
    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }

}