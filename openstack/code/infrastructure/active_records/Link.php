<?php
/**
 * Copyright 2014 Openstack Foundation
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
class Link extends DataObject {

	private static $db = [
		'Label' => 'Text',
		'URL' => 'Text',
		'IconClass' => 'Varchar(50)',
		'ButtonColor' => 'Color',
		'Description' => 'HTMLText'
	];

	static $singular_name = 'Link';
	static $plural_name = 'Links';
	
	private static $summary_fields = array(
	      'Label' => 'Label', 
	      'URL' => 'URL'
	   );

	function getCMSFields() {
		$fields = new FieldList (
			new TextField('Label','Label this link (this will be the text displayed):'),
			new TextField ('URL','Full URL (ex: http://www.photos.com/photo.jpg) for image'),
			new TextField ('IconClass','Fontawesome class for the label icon (optional)'),
			new ColorField ('ButtonColor','Button Color (optional)'),
			new TextField ('Description','Short description / text for this link (optional)')
		);
		return $fields;
	}

}