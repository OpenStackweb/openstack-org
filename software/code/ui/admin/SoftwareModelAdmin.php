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
class SoftwareModelAdmin extends ModelAdmin
{
    public static $managed_models = array
    (
        'OpenStackComponent',
        'OpenStackRelease',
        'OpenStackSampleConfig',
    );

    public $showImportForm = false;

    private static $url_segment = 'software';
    private static $menu_title  = 'Software';

    public function init()
    {
        parent::init();
    }

    public function getEditForm($id = null, $fields = null) {

        $form = parent:: getEditForm($id, $fields);

        if($this->modelClass === 'OpenStackComponent' || $this->modelClass === 'OpenStackSampleConfig') {
            $gridField = $form->Fields()->fieldByName($this->sanitiseClassName($this->modelClass));
            $config = $gridField->getConfig();
            $config->addComponent(new GridFieldSortableRows('Order'));
        }
        if($this->modelClass === 'OpenStackRelease') {
            $gridField = $form->Fields()->fieldByName($this->sanitiseClassName($this->modelClass));
            $config = $gridField->getConfig();
            $config->addComponent(new GridFieldCloneReleaseAction());
        }
        return $form;
    }
}