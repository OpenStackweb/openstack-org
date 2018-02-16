<?php
/**
 * Copyright 2018 OpenStack Foundation
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

final class AddSummitCalendarSyncErrorEmailMigration extends AbstractDBMigrationTask
{
    protected $title = "AddSummitCalendarSyncErrorEmailMigration";

    protected $description = "AddSummitCalendarSyncErrorEmailMigration";

    function doUp()
    {
        if(intval(PermamailTemplate::get()->filter('Identifier', SUMMIT_CALENDAR_SYNC_ERROR_EMAIL_TEMPLATE)->count()) === 0 )
        {
            $email_tpl             = PermamailTemplate::create();
            $email_tpl->Identifier = SUMMIT_CALENDAR_SYNC_ERROR_EMAIL_TEMPLATE;
            $email_tpl->Subject    = '';
            $email_tpl->From       = 'noreply@openstack.org';
            $body =<<<HTML
            <html>
<body>

<p>Hi {\$Member.FullName},</p>

<p>OpenStack Summit {\$Summit.Title} Calendar is using your {\$Provider} calendar.</p>

<p>We need you to relink your {\$Provider} account for {\$Member.Email} so that OpenStack Summit {\$Summit.Title} Calendar can continue to access your calendar.

This can happen for all sorts for reasons. Often, for example, providers will expire links to services when you change your password.</p>

<a href="{\$CalendarSyncUrl}">Relink {\$Provider} calendar</a>
</body>
</html>
HTML;

            $email_tpl->Content    = $body;
            $email_tpl->write();
        }

    }
}