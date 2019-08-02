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

/**
 * Class SummitAdmin
 */
final class SummitAdmin extends ModelAdmin implements PermissionProvider
{
    private static $url_segment = 'summits';

    public $showImportForm = false;

    private static $managed_models =
    [
        'Summit',
        'SummitType',
        'SponsorshipType',
        'DefaultSummitEventType'  => ['title' => "Default Event Types"],
        'DefaultTrackTagGroup'    => ['title' => "Default Track Tag Groups"],
    ];

    public function init()
    {
        parent::init();
        Requirements::javascript('summit/javascript/GridFieldApprovePushNotificationAction.js');
        Requirements::javascript('summit/code/admins/SummitAdmin.js');
        $res = Permission::check("ADMIN") || Permission::check("ADMIN_SUMMIT_APP") || Permission::check("ADMIN_SUMMIT_APP_SCHEDULE");
        if (!$res) {
            Security::permissionFailure();
        }
    }

    private static $menu_title = 'Summits';

    public function index($request) {
        if($request->getURL() == 'admin/summits'){
            return Controller::curr()->redirect("admin/summits/Summit");
        }
        return parent::index($request);
    }


    public function providePermissions() {
        return [
            'ADMIN_SUMMIT_APP' => [
                'name'     => 'Full Access to Summit CMS Admin',
                'category' => 'Summit Application',
                'help'     => '',
                'sort'     => 0
            ],
            'ADMIN_SUMMIT_APP_SCHEDULE' => [
                'name'     => 'Full Access to Summit CMS Schedule Admin',
                'category' => 'Summit Application',
                'help'     => '',
                'sort'     => 1
            ],
        ];
    }

    public function getEditForm($id = null, $fields = null) {

        $form = parent:: getEditForm($id, $fields);

        if($this->modelClass === 'DefaultSummitEventType') {
            $gridField = $form->Fields()->fieldByName($this->sanitiseClassName($this->modelClass));
            $config = $gridField->getConfig();
            $config->removeComponentsByType('GridFieldAddNewButton');
            $config->removeComponentsByType('GridFieldExportButton');
            $config->removeComponentsByType('GridFieldPrintButton');
            $multi_class_selector = new GridFieldAddNewMultiClass();
            $multi_class_selector->setClasses
            (
                [
                    'DefaultSummitEventType'  => 'Default Event Type',
                    'DefaultPresentationType' => 'Default Presentation Type',
                ]
            );
            $config->addComponent($multi_class_selector);
        }

        return $form;
    }
}