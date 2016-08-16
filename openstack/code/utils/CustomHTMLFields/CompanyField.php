<?php

/**
 * Copyright 2016 OpenStack Foundation
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

class CompanyField extends CompositeField
{


    public function __construct($name, $title, $value = null)
    {
        $this->name  = $name;
        $this->title = $title;
        $children    = new FieldList();

        $source      = Company::get()->sort('Name')->map('ID', 'Name')->toArray();
        $source['0'] = "-- New Company --";

        $children->add($ddl = new DropdownField($name . '_id', $title, $source));
        $ddl->setEmptyString('-- Select Your Company --');
        $ddl->addExtraClass('select-company-name');
        if (!is_null($value)) {
            $c = Company::get()->filter('Name', $value)->first();
            if ($c) $ddl->setValue($c->ID);
        }
        $children->add($txt = new TextField($name, ''));
        $txt->addExtraClass('input-company-name');
        parent::__construct($children);

        $control_css_class = strtolower('company-composite');
        $this->addExtraClass($control_css_class);

        Requirements::javascript('openstack/code/utils/CustomHTMLFields/js/company.field.js');

        Requirements::customScript("
        jQuery(document).ready(function($) {
            $('.'+'{$control_css_class}').company_field();
        });");
    }
}