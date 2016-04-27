<?php
/**
 * Copyright 2014 Openstack Foundation
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

class SummitReport extends DataObject implements ISummitReport
{
    private static $db = array
    (
        'Name'        => 'Text',
        'Description' => 'HTMLText',
    );

    private static $has_many = array
    (
        'Configurations' => 'SummitReportConfig'
    );



    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    public function getConfigByName($name) {
        return $this->Configurations()->filter('Name', $name)->first()->Value;
    }

    public function setConfigByName($name,$value) {
        $config = $this->Configurations();
        if (!$config->count()) {
            $config_obj = new SummitReportConfig();
            $config_obj->Name = $name;
            $config_obj->Value = $value;
            $this->Configurations()->add($config_obj);
        } else {
            $config_obj = $config->filter('Name', $name)->first();
            $config_obj->Value = $value;
            $config_obj->write();
        }
    }


}