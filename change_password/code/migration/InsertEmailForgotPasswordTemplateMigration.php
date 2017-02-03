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
class InsertEmailForgotPasswordTemplateMigration extends AbstractDBMigrationTask
{
    protected $title = "Insert Forgot Password Email Template";

    protected $description = "Insert Forgot Password Email Template";

    function doUp()
    {
        if(intval(PermamailTemplate::get()->filter('Identifier', MEMBER_FORGOT_PASSWORD_EMAIL_TEMPLATE_ID)->count()) === 0 )
        {
            $email_tpl             = PermamailTemplate::create();
            $email_tpl->Identifier = MEMBER_FORGOT_PASSWORD_EMAIL_TEMPLATE_ID;
            $email_tpl->Subject    = 'OpenStack.org - Your password reset link';
            $email_tpl->From       = 'noreply@openstack.org';
            $email_tpl->Content    ='<p>Dear {$Member.FullName},</p><p>Here is your <a href="{$PasswordResetLink}">password reset link</a> for the OpenStack website. </p>';
            $email_tpl->write();
        }
    }

    function doDown()
    {

    }
}