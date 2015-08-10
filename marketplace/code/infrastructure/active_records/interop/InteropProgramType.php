<?php
/**
 * Copyright 2015 Openstack Foundation
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

class InteropProgramType extends DataObject
{

    static $db = array(
        'Name' => 'Varchar',
        'ShortName' => 'Varchar',
        'Order' => 'Int',
        'RequiredCode' => 'HTMLText',
        'ProductExamples' => 'HTMLText',
        'TrademarkUse' => 'HTMLText',
        'HasCapabilities' => 'Boolean',
    );

    static $summary_fields = array (
        'Name' => 'Name',
        'ShortName' => 'Short Name',
        'Order' => 'Order'
    );

    function getCMSFields()
    {
        $fields =  new FieldList();
        $fields->add(new TextField('Name','Name'));
        $fields->add(new TextField('ShortName','Short Name'));
        $fields->add(new CheckboxField('HasCapabilities','Has Capabilities?'));
        $fields->add(new TextField('Order','Order'));
        $fields->add(new HtmlEditorField('RequiredCode','Required Code'));
        $fields->add(new HtmlEditorField('ProductExamples','Product Examples'));
        $fields->add(new HtmlEditorField('TrademarkUse','Trademark Use'));

        return $fields;
    }


}