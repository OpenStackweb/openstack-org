<?php
/**
 * Copyright 2019 OpenStack Foundation
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
use \Openstack\DDD\Model\Entity;

/**
 * Class PersonalCalendarShareInfo
 */
final class PersonalCalendarShareInfo extends DataObject
{
    use Entity;

    private static $db = [
        'Hash'     => 'Varchar(512)',
        'Revoked'  => 'Boolean'
    ];

    private static $has_one = [
        'Summit' => 'Summit',
        'Owner'  => 'Member',
    ];

    /**
     * @return bool
     */
    public function isRevoked():bool
    {
        return boolval($this->getField('Revoked'));
    }

    public function revoke(){
        $this->Revoked = true;
        $this->write();
    }
}