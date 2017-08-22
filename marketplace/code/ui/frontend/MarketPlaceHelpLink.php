<?php
/**
 * Copyright 2017 Openstack Foundation
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
class MarketPlaceHelpLink extends DataObject {

    private static $db = array(
        'Label'     => 'Varchar(255)',
        'Link'      => 'Varchar(255)',
        'SortOrder' => 'Int'
    );

    private static $has_one = array(
        'MarketPlacePage'  => 'MarketPlacePage',
    );

	private static $default_sort = 'SortOrder';

    private static $summary_fields = array
    (
        'Label'  => 'Label',
        'Link'    => 'Link',
    );

    function getCMSFields(){

	    $fields = new FieldList;

	    $fields->push(new TextField('Label'));
	    $fields->push(new TextField('Link'));

        return $fields;
    }

    function getValidator()	{
        $validator= new FileRequiredFields(array('Label','Link'));
        return $validator;
    }

}