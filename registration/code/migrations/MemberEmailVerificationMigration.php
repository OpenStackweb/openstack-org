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
final class MemberEmailVerificationMigration extends AbstractDBMigrationTask
{
    protected $title = "MemberEmailVerificationMigration";

    protected $description = "create the default email templates for member email vefication";

    function doUp()
    {
        global $database;

        if(intval(PermamailTemplate::get()->filter('Identifier', MEMBER_REGISTRATION_VERIFICATION_EMAIL_TEMPLATE_ID)->count()) === 0 )
        {
            $email_tpl             = PermamailTemplate::create();
            $email_tpl->Identifier = MEMBER_REGISTRATION_VERIFICATION_EMAIL_TEMPLATE_ID;
            $email_tpl->Subject    = 'Thank you for becoming an OpenStack Foundation Member';
            $email_tpl->From       = 'noreply@openstack.org';
            $email_tpl->Content    ='<p>Dear {$Member.FullName}, thank you for joining the OpenStack Foundation.</p><p>IMPORTANT! Please click <a href="{$VerificationLink}">here</a> to complete your registration.</p>';
            $email_tpl->write();
        }

        if(intval(PermamailTemplate::get()->filter('Identifier', MEMBER_REGISTRATION_VERIFIED_EMAIL_TEMPLATE_ID)->count()) === 0 )
        {
            $email_tpl             = PermamailTemplate::create();
            $email_tpl->Identifier = MEMBER_REGISTRATION_VERIFIED_EMAIL_TEMPLATE_ID;
            $email_tpl->Subject    = 'Thank you for becoming an OpenStack Foundation Member';
            $email_tpl->From       = 'noreply@openstack.org';
            $email_tpl->Content    ='<p>Dear {$Member.FullName}, thank you for registering with OpenStack.</p>
<div style="font-size: 10px;">
<ul><li>Meet the <a href="http://www.openstack.org/foundation/staff">OpenStack Foundation Staff</a>, the <a href="http://www.openstack.org/foundation/board-of-directors/">Board of Directors</a>, and the <a href="http://www.openstack.org/foundation/technical-committee/">Technical Committee</a>.</li>
<li>Using OpenStack?  <a href="http://openstack.org/user-survey" target="_blank">Take the User Survey</a>.</li>
<li>The latest Summit information is always <a href="http://openstack.org/Summit" target="_blank">available here</a>, including links to video of past sessions.</li>
<li>Ready to contribute to the future of OpenStack? <a href="https://wiki.openstack.org/wiki/How_To_Contribute" target="_blank">Check the how to contribute page </a>.</li>
</ul></div>';
            $email_tpl->write();
        }
    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}