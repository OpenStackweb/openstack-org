<?php
/**
 * Copyright 2018 Openstack Foundation
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
class PageSection extends DataObject {

	static $db = array(
		'Name' => 'Varchar(255)',
		'Label' => 'Varchar(255)',
		'Text' => 'HTMLText',
		'IconClass' => 'Varchar(50)'
	);
	
	static $has_one = array(
		'Picture' => 'BetterImage'
	);
	
	static $singular_name = 'Section';
	static $plural_name = 'Sections';
	
	static $summary_fields = array( 
        'Label' => 'Label'
    );

    function getCMSFields() {
        $join_image = new UploadField('Picture', 'Picture instead of text');
        $join_image->setAllowedMaxFileNumber(1);

        $fields = new FieldList (
            new TextField('Name','Name this section (no spaces):'),
            new TextField('Label','Label to show:'),
            new TextField ('IconClass','Fontawesome class for the label icon (optional)'),
            new HtmlEditorField('Text','Text to display (optional)'),
            $join_image
        );
        return $fields;
    }
}