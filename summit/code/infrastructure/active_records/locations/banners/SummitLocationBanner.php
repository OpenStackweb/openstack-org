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
 * Class SummitLocationBanner
 */
class SummitLocationBanner extends DataObject
{
    private static $db = [
        'Title'   => 'Text',
        'Content' => 'HTMLText',
        'Enabled' => 'Boolean',
        'Type'    => 'Enum(array("Primary","Secondary"), "Primary")',
    ];

    private static $has_one = [
        'Location' => 'SummitAbstractLocation',
    ];

    private static $summary_fields = [
        'Title',
        'Enabled'
    ];

    protected function validate()
    {
        $valid = parent::validate();

        if(!$valid->valid()) return $valid;

        if(empty($this->Title)){
            return $valid->error('Title  is mandatory!');
        }

        if(empty($this->Content)){
            return $valid->error('Content  is mandatory!');
        }

        return $valid;
    }

}