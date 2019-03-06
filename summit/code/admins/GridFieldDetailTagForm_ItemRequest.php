<?php
/**
 * Copyright 2019 OpenStack Foundation
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
 * Class GridFieldDetailTagForm_ItemRequest
 */
final class GridFieldDetailTagForm_ItemRequest extends GridFieldDetailForm_ItemRequest {

    public function doSave($data, $form) {
        $new_record = $this->record->ID == 0;
        $controller = $this->getToplevelController();
        $list = $this->gridField->getList();

        if(!$this->record->canEdit()) {
            return $controller->httpError(403);
        }

        if (isset($data['ClassName']) && $data['ClassName'] != $this->record->ClassName) {
            $newClassName = $data['ClassName'];
            // The records originally saved attribute was overwritten by $form->saveInto($record) before.
            // This is necessary for newClassInstance() to work as expected, and trigger change detection
            // on the ClassName attribute
            $this->record->setClassName($this->record->ClassName);
            // Replace $record with a new instance
            $this->record = $this->record->newClassInstance($newClassName);
        }

        try {
            $form->saveInto($this->record);
            $this->record->write();
            $extraData = $this->getExtraSavedData($this->record, $list);
            $list->add($this->record, $extraData);
            $category_seed_message = $this->addToAllTracks($this->record, $controller->getRequest()->postVar('SummitID'));
        } catch(ValidationException $e) {
            $form->sessionMessage($e->getResult()->message(), 'bad', false);
            if ($controller->getRequest()->isAjax()) {
                $responseNegotiator = new PjaxResponseNegotiator(array(
                    'CurrentForm' => function () use (&$form) {
                        return $form->forTemplate();
                    },
                ));
                $controller->getRequest()->addHeader('X-Pjax', 'CurrentForm');
                return $responseNegotiator->respond($controller->getRequest());
            }
            Session::set("FormInfo.{$form->FormName()}.errors", array());
            Session::set("FormInfo.{$form->FormName()}.data", $form->getData());
            return $controller->redirectBack();
        }

        // TODO Save this item into the given relationship

        $link = '<a href="' . $this->Link('edit') . '">"'
            . htmlspecialchars($this->record->Title, ENT_QUOTES)
            . '"</a>';
        $message = _t(
            'GridFieldDetailForm.Saved',
            'Saved {name} {link}',
            array(
                'name' => $this->record->i18n_singular_name(),
                'link' => $link
            )
        );

        $message = $message . ' <br> '.$category_seed_message;
        $form->sessionMessage($message, 'good', false);

        if($new_record) {
            return $controller->redirect($this->Link());
        } elseif($this->gridField->getList()->byId($this->record->ID)) {
            // Return new view, as we can't do a "virtual redirect" via the CMS Ajax
            // to the same URL (it assumes that its content is already current, and doesn't reload)
            return $this->edit($controller->getRequest());
        } else {
            // Changes to the record properties might've excluded the record from
            // a filtered list, so return back to the main view if it can't be found
            $noActionURL = $controller->removeAction($data['url']);
            $controller->getRequest()->addHeader('X-Pjax', 'Content');
            return $controller->redirect($noActionURL, 302);
        }
    }

    public function doSaveAndQuit($data, $form) {
        Controller::curr()->getResponse()->addHeader("X-Pjax","Content");

        $new_record = $this->owner->record->ID == 0;
        $controller = Controller::curr();
        $list = $this->owner->gridField->getList();

        if($list instanceof ManyManyList) {
            // Data is escaped in ManyManyList->add()
            $extraData = (isset($data['ManyMany'])) ? $data['ManyMany'] : null;
        } else {
            $extraData = null;
        }

        if(!$this->owner->record->canEdit()) {
            return $controller->httpError(403);
        }

        try {
            $form->saveInto($this->owner->record);
            $this->owner->record->write();
            $list->add($this->owner->record, $extraData);
            $this->addToAllTracks($this->record, $controller->getRequest()->postVar('SummitID'));
        } catch(ValidationException $e) {
            $form->sessionMessage($e->getResult()->message(), 'bad');
            $responseNegotiator = new PjaxResponseNegotiator(array(
                'CurrentForm' => function() use(&$form) {
                    return $form->forTemplate();
                },
                'default' => function() use(&$controller) {
                    return $controller->redirectBack();
                }
            ));
            if($controller->getRequest()->isAjax()){
                $controller->getRequest()->addHeader('X-Pjax', 'CurrentForm');
            }
            return $responseNegotiator->respond($controller->getRequest());
        }

        return Controller::curr()->redirect($this->getBackLink());
    }

    public function addToAllTracks($tag, $summitID) {
        if ($tag && $summitID) {
            Summit::seedTagOnAllTracksAllowedTags(
                Summit::get()->byID($summitID),
                $tag
            );

            return 'Tag added to all categories.';
        } else {
            return 'ERROR: Could not add tag to categories!!';
        }

    }

}

