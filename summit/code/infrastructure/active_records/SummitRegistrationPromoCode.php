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
class SummitRegistrationPromoCode extends DataObject implements ISummitRegistrationPromoCode
{
    private static $db = array
    (
        'Code' => 'Varchar(255)',
    );

    private static $has_one = array(
        'Summit' => 'Summit',
        'Owner'  => 'Member'
    );

    private static $indexes = array(
        'SummitID_Code_Owner' => array('type'=>'unique', 'value'=>'SummitID,Code, OwnerID')
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        // TODO: Implement getIdentifier() method.
    }

    /**
     * @return string
     */
    public function code()
    {
        // TODO: Implement code() method.
    }

    /**
     * @return Summit
     */
    public function summit()
    {
        // TODO: Implement summit() method.
    }

    /**
     * @return ICommunityMember
     */
    public function owner()
    {
        // TODO: Implement owner() method.
    }

}