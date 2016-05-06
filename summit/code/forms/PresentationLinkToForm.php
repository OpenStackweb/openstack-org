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
class PresentationLinkToForm extends Form
{

    protected $presentation;

    public function __construct($controller, $name, SummitEvent $presentation)
    {
        $this->presentation = $presentation;
        
        $fields = FieldList::create(
            TextField::create('Link', 'Link (URL) for your online presentation:')
        );

        $actions = FieldList::create(
            FormAction::create('saveLink', 'Save Link')
        );
        
        $validator = RequiredFields::create(['Link']);        

        parent::__construct($controller, $name, $fields, $actions, $validator);

        $material = $presentation->MaterialType('PresentationSlide');

        if($material) {
        	$this->loadDataFrom($material);	
        }        
    }

    public function forTemplate()
    {
        return $this->renderWith([
            $this->class,
            'Form'
        ]);
    }

    public function saveLink($data, $form)
    {
        $url = $data['Link'];

        // Attach a protocol if needed
        if (substr($url, 0, 7) != 'http://' && substr($url, 0, 8) != 'https://') {
            $url = 'http://' . $url;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $form->sessionMessage('That does not appear to be a valid URL', 'bad');

            return $this->controller()->redirectBack();
        }

    	$material = PresentationSlide::create();
    	$material->Link = $url;
    	$material->write();
		$this->presentation->Materials()->filter([
			'ClassName' => 'PresentationSlide'
		])->removeAll();
    	
    	$this->presentation->Materials()->add($material);
    	$token = SecurityToken::inst()->getValue();

        return $this->controller()->redirect(Controller::join_links(
    		$this->controller()->Link(),
    		'success',
    		"?key={$token}&material={$material->ID}"
        ));
    }

}