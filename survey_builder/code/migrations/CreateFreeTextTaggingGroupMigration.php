<?php

/**
 * Copyright 2017 OpenStack Foundation
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
final class CreateFreeTextTaggingGroupMigration extends AbstractDBMigrationTask
{
    protected $title = "Create Survey Free Text Tagging Group";

    protected $description = "creates group survey-free-text-tagging.";

    function doUp()
    {
        global $database;
        //$members = array(93000); // members to add

        $group = new Group();
        $group->setTitle('Free Text Tagging');
        $group->setDescription('Allows access to sangria survey free text tagging page.');
        $group->setSlug('survey-free-text-tagging');
        $group->write();

        //$group->Members()->setByIDList($members);

        Permission::grant($group->getIdentifier(), 'FREE_TEXT_TAGGING_ACCESS');

        // update sangria group
        $sangria_group = Group::get()->filter('Code','sangria')->first();
        Permission::grant($sangria_group->getIdentifier(), 'FREE_TEXT_TAGGING_ACCESS');

    }

    function doDown()
    {

    }
}
