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
final class TagGroupMigration extends AbstractDBMigrationTask
{
    protected $title = "TagGroupMigration";

    protected $description = "TagGroupMigration";

    function doUp()
    {
        global $database;

        $tag_groups = array (
            'session type'                  => 'Session Type',
            'other open source projects'    => 'Other Open Source Projects',
            'topics'                        => 'Topics',
            'speaker'                       => 'Speaker',
            'openstack projects mentioned'  => 'OpenStack Projects Mentioned'
        );

        $order = 1;

        foreach($tag_groups as $name => $label) {
            if (TagGroup::get()->filter('Name', $name)->count() == 0) {
                $tag_group = new TagGroup();
                $tag_group->Name = $name;
                $tag_group->Order = $order;
                $tag_group->Label = $label;
                $tag_group->write();
            }
            $order ++;
        }

    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}