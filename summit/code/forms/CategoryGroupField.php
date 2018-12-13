<?php

/**
 * Copyright 2015 OpenStack Foundation
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
class CategoryGroupField extends DropdownField
{
    public function Field($properties = array()) {
        $source = $this->getSource();
        $options = array();
        if($source) {
            // SQLMap needs this to add an empty value to the options
            if(is_object($source) && $this->emptyString) {
                $options[] = new ArrayData(array(
                    'Value' => '',
                    'Title' => $this->emptyString
                ));
            }

            foreach($source as $data) {
                $selected = false;
                $value = $data['id'];
                if($value === '' && ($this->value === '' || $this->value === null)) {
                    $selected = true;
                } else {
                    // check against value, fallback to a type check comparison when !value
                    if($value) {
                        $selected = ($value == $this->value);
                    } else {
                        $selected = ($value === $this->value) || (((string) $value) === ((string) $this->value));
                    }

                    $this->isSelected = $selected;
                }

                $disabled = false;
                if(in_array($value, $this->disabledItems) && $data['title'] != $this->emptyString ){
                    $disabled = 'disabled';
                }

                $options[] = new ArrayData(array(
                    'Title' => $data['title'],
                    'Value' => $value,
                    'Selected' => $selected,
                    'Disabled' => $disabled,
                ));
            }
        }

        $properties = array_merge($properties, array('Options' => new ArrayList($options)));

        $context = $this;

        if(count($properties)) {
            $context = $context->customise($properties);
        }

        $this->extend('onBeforeRender', $this);

        return $context->renderWith($this->getTemplates());
    }

    public function validate($validator) {
        $source = array_column($this->getSourceAsArray(), 'id');
        $disabled = $this->getDisabledItems();


        if (!in_array($this->value, $source) || in_array($this->value, $disabled)) {
            if ($this->getHasEmptyDefault() && !$this->value) {
                return true;
            }
            $validator->validationError(
                $this->name,
                _t(
                    'DropdownField.SOURCE_VALIDATION',
                    "Please select a value within the list provided. {value} is not a valid option",
                    array('value' => $this->value)
                ),
                "validation"
            );
            return false;
        }
        return true;
    }
}