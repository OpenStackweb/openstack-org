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
/**
 * Class SummitEventFeedbackForm
 */
final class SummitEventFeedbackForm extends BootstrapForm {

	function __construct($controller, $name, $use_actions = true) {

        $RatingField = new TextField('rating','');
        $RatingField->setValue(0);
        $CommentField = new HtmlEditorField('comment','Comment');
        $CommentField->setRows(8);
        $sec_field = new TextField('field_98438688','field_98438688');
        $sec_field->addExtraClass('honey');

        $fields = new FieldList (
            $RatingField,
            $CommentField,
            $sec_field
        );

		// Create action
		$actions = new FieldList();
		if($use_actions)
			$actions->push(new FormAction('submit', 'Submit'));

		$this->addExtraClass('review-form');

        $css =<<<CSS
.honey {
	position: absolute; left: -9999px
}
CSS;
        Requirements::customCSS($css, 'honey_css');

		parent::__construct($controller, $name, $fields, $actions);
	}

	function forTemplate() {
		return $this->renderWith(array(
			$this->class,
			'Form'
		));
	}
}
