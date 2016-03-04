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
class CreateSummitFrontEndAdminGroupMigration extends AbstractDBMigrationTask
{
    protected $title = "CreateSummitFrontEndAdminGroupMigration";

    protected $description = "CreateSummitFrontEndAdminGroupMigration";

    function doUp()
    {
        global $database;
        if(intval(Group::get()->filter('Code', 'SUMMIT_FRONT_END_ADMINISTRATORS')->count()) > 0) return;
        $g = Group::create();
        $g->setTitle('Summit Front End Administrators');
        $g->setDescription('Allows to Access to summit-admin application');
        $g->setSlug('SUMMIT_FRONT_END_ADMINISTRATORS');
        $g->write();

        Permission::grant($g->getIdentifier(), 'ADMIN_SUMMIT_APP_FRONTEND_ADMIN');
    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}