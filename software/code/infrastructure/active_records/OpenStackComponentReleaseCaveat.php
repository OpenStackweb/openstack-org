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
final class OpenStackComponentReleaseCaveat extends DataObject
{

    static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

    static $db = array
    (
        'Status'      => 'Text',
        'Label'       => 'Text',
        'Description' => 'Text',
        'Type'        => "Enum('NotSet, InstallationGuide, QualityOfPackages, ProductionUse, SDKSupport', 'NotSet')"
    );

    static $has_one = array
    (
        'Release'   => 'OpenStackRelease',
        'Component' => 'OpenStackComponent',
    );

    public function getTypeI18n() 
    {
    	return _t('Software.RELEASE_CAVEAT_TYPE_'.strtoupper($this->Type), $this->Type);
    }

}