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
class PresentationMediaUploadForm extends Form
{

    /**
     * @var SummitEvent
     */
    protected $presentation;

    /**
     * PresentationMediaUploadForm constructor.
     * @param Controller $controller
     * @param string $name
     * @param SummitEvent $presentation
     */
    public function __construct($controller, $name, SummitEvent $presentation)
    {
        $this->presentation = $presentation;

        $fields = FieldList::create(
            FileAttachmentField::create('Slide', 'File')
                ->setFolderName('/presentation-media/')
                ->setMaxFilesize(30) // set up to 30 MB
                ->setPermissions([
                    'upload' => true,
                    'detach' => false,
                    'delete' => false
                ])
        );

        $actions = FieldList::create(
            FormAction::create('doUpload', 'Upload File')
        );

        $validator = RequiredFields::create(['Slide']);

        parent::__construct($controller, $name, $fields, $actions, $validator);

        $material = $presentation->MaterialType('PresentationSlide');

        if ($material) {
            $this->loadDataFrom($material);
        }
    }

    /**
     * @return HTMLText
     */
    public function forTemplate()
    {
        return $this->renderWith([
            $this->class,
            'Form'
        ]);
    }

    /**
     * @param $data
     * @param $form
     * @return mixed
     */
    public function doUpload($data, $form)
    {
        $material          = PresentationSlide::create();
        $material->Name    = $this->presentation->Title;
        $material->SlideID = $data['Slide'];
        $material->write();

        $this->presentation->Materials()->filter([
            'ClassName' => 'PresentationSlide'
        ])->removeAll();
        $this->presentation->Materials()->add($material);
        $token = SecurityToken::inst()->getValue();

        return $form->controller()->redirect(Controller::join_links(
            $form->controller()->Link(),
            'success',
            "?key={$token}&material={$material->ID}"
        ));
    }

}