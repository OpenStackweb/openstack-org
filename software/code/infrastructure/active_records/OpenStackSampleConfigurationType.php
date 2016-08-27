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
class OpenStackSampleConfigurationType extends DataObject
{

    private static $summary_fields = array
    (
        'Type',
        'IsDefault'
    );

    private static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

    private static $db = array
    (
        'Type'      => 'Text',
        'Order'     => 'Int',
        'IsDefault' => 'Boolean',
    );

    private static $has_one = array
    (
        'Release' => 'OpenStackRelease',
    );

    private static $has_many = array
    (
        'SampleConfigurations' => 'OpenStackSampleConfig'
    );

    protected function onBeforeDelete() {
        parent::onBeforeDelete();
        foreach($this->SampleConfigurations() as $item) {
            $item->delete();
        }
    }

    public function getDefaultSampleConfiguration()
    {
        return $this->SampleConfigurations()->filter('isDefault', true)->first();
    }
}