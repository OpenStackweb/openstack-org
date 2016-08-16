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

/**
 * Class JobAdmin
 */
final class JobAdmin extends ModelAdmin implements PermissionProvider
{
    private static $url_segment = 'jobs';

    public $showImportForm = false;

    private static $managed_models = array
    (
        'JobType'
    );

    public function init()
    {
        parent::init();
        $res = Permission::check("ADMIN") || Permission::check("ADMIN_JOBS");
        if (!$res) {
            Security::permissionFailure();
        }
    }

    private static $menu_title = 'Jobs';

    public function providePermissions() {
        return array(
            'ADMIN_JOBS' => array(
                'name'     => 'Full Access to Jobs CMS Admin',
                'category' => 'Jobs Admin',
                'help'     => '',
                'sort'     => 0
            ),

        );
    }
}