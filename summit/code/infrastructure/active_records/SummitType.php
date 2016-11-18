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
class SummitType extends DataObject
{
    use Colorable;

    private static $db = array
    (
        'FriendlyName' => 'Text',
        'Description'  => 'HTMLText',
        'Audience'     => 'Text',
        'Color'        => 'Text',
        'Type'         => 'VarChar(100)',
    );

    private static $has_many = array
    (
    );

    private static $defaults = array
    (
    );

    private static $has_one = array
    (
    );

    private static $summary_fields = array
    (
        'FriendlyName',
        'Audience',
        'Type'
    );

    private static $searchable_fields = array
    (
    );

    protected function validate()
    {
        $valid = parent::validate();
        if(!$valid->valid()) return $valid;

        //validate SLUG Format

        $slug = trim($this->Type);

        if(empty($slug)){
            return $valid->error("Type is Mandatory!.");
        }

        if(preg_match("/^[A-Z0-9\_\-]*$/", $slug) == 0){
            return $valid->error("Type is format is invalid (no spaces, uppercase letter or numbers)!.");
        }

        $count = intval(SummitType::get()->filter(array('Type' => $slug, 'ID:ExactMatch:not' => $this->ID))->count());

        if($count > 0)
            return $valid->error(sprintf('Summit Type %s already exists!. please set another one', $this->Type));

        return $valid;
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

    public function getCMSFields()
    {

        $f = new FieldList
        (
            $rootTab = new TabSet("Root", $tabMain = new Tab('Main'))
        );

        $f->addFieldToTab('Root.Main', new TextField('FriendlyName','Friendly Name'));
        $f->addFieldToTab('Root.Main', new TextField('Type','Type (Slug)'));
        $f->addFieldToTab('Root.Main', new HtmlEditorField('Description','Description'));
        $f->addFieldToTab('Root.Main', new ColorField("Color","Color"));
        $f->addFieldToTab('Root.Main', new TextField('Audience','Audience'));

        return $f;
    }
}