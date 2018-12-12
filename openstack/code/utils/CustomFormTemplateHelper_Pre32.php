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
final class CustomFormTemplateHelper_Pre32 extends FormTemplateHelper_Pre32
{
    /**
     * @param Form $form
     *
     * @return string
     */
    public function generateFormID($form) {
        if(is_subclass_of(Controller::curr(), "LeftAndMain")) {
            if ($id = $form->getHTMLID()) {
                return Convert::raw2htmlid($id);
            }

            return Convert::raw2htmlid(
                get_class($form) . '_' . str_replace(array('.', '/'), '', $form->getName())
            );
        }

        return parent::generateFormID($form);
    }

    /**
     * @param FormField $field
     *
     * @return string
     */
    public function generateFieldHolderID($field) {
        if(is_subclass_of(Controller::curr(), "LeftAndMain")) {
            return $this->generateFieldID($field) . '_Holder';
        }
        return parent::generateFieldHolderID($field);
    }

    /**
     * Generate the field ID value
     *
     * @param FormField
     *
     * @return string
     */
    public function generateFieldID($field) {
        if(is_subclass_of(Controller::curr(), "LeftAndMain")) {
            if ($form = $field->getForm()) {
                return sprintf("%s_%s",
                    $this->generateFormID($form),
                    Convert::raw2htmlid($field->getName())
                );
            }

            return Convert::raw2htmlid($field->getName());
        }

        return parent::generateFieldID($field);
    }
}