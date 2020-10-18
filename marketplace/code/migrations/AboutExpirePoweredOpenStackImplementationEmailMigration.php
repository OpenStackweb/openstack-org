<?php

/**
 * Copyright 2017 Open Infrastructure Foundation
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
final class AboutExpirePoweredOpenStackImplementationEmailMigration extends AbstractDBMigrationTask
{
    protected $title = "AboutExpirePoweredOpenStackImplementationEmailMigration";

    protected $description = "AboutExpirePoweredOpenStackImplementationEmailMigration";

    function doUp()
    {
      if(intval(PermamailTemplate::get()->filter('Identifier', EXPIRED_POWERED_OPENSTACK_IMPLEMENTATION_EMAIL)->count()) === 0 )
        {
            $email_tpl             = PermamailTemplate::create();
            $email_tpl->Identifier = EXPIRED_POWERED_OPENSTACK_IMPLEMENTATION_EMAIL;
            $email_tpl->Subject    = 'Powered OpenStack Implementations - About to Expire Digest';
            $email_tpl->From       = 'noreply@openstack.org';
$body =<<<HTML
<html>
<body>
    <p>Hello, following Powered OpenStack Implementations are about to expire on next 90 Days</p>
    <ul>
    <% loop \$AboutExpiredOpenStackPoweredImplementations %>
        <li>
            <a href="/sangria/ViewPoweredOpenStackProductDetail/\$ID">{\$Name} - {\$CompanyName}</a>
        </li>
    <% end_loop %>
    </ul>
    Cheers.
</body>
</html>
HTML;

            $email_tpl->Content    = $body;
            $email_tpl->write();
        }

    }
}