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
class SummitEventType extends DataObject implements ISummitEventType
{

    private static $db = array
    (
        'Type' => 'Text',
        'Color' => 'Text'
    );

    private static $has_many = array
    (
    );

    private static $defaults = array
    (
    );

    private static $has_one = array
    (
        'Summit' => 'Summit'
    );

    private static $summary_fields = array
    (
        'Type'
    );

    private static $searchable_fields = array
    (
        'Type'
    );
    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getField('Type');
    }

    /**
     * @return string
     */
    public function getColor()
    {
        $color = $this->getField('Color');
        if (strpos($color,'#') === false) {
            $color = '#'.$color;
        }
        return $color;
    }

    public function canDelete($member=null) {
        if ($this->getType() == 'Presentation') {
            return false;
        }
        return parent::canDelete($member);
    }

    public function getCMSFields() {
        $fields = new FieldList();
        $fields->add(new TextField('Type','Type'));
        $fields->add(new ColorField("Color","Color"));
        $fields->add(new HiddenField('SummitID','SummitID'));
        return $fields;
    }

    /**
     * @param Member $member
     * @return boolean
     */
    public function canView($member = null) {
        return Permission::check("ADMIN") || Permission::check("ADMIN_SUMMIT_APP") || Permission::check("ADMIN_SUMMIT_APP_SCHEDULE");
    }

    /**
     * @param Member $member
     * @return boolean
     */
    public function canEdit($member = null) {
        return Permission::check("ADMIN") || Permission::check("ADMIN_SUMMIT_APP") || Permission::check("ADMIN_SUMMIT_APP_SCHEDULE");
    }
}