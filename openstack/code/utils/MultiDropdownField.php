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
final class MultiDropdownField extends FormField {

	protected $selectedValues = array();
	/**
	 * @var boolean $source Associative or numeric array of all dropdown items,
	 * with array key as the submitted field value, and the array value as a
	 * natural language description shown in the interface element.
	 */
	protected $source;

	/**
	 * @var boolean $disabled
	 */
	protected $disabled;

	/**
	 * @var boolean $hasEmptyDefault Show the first <option> element as
	 * empty (not having a value), with an optional label defined through
	 * {@link $emptyString}. By default, the <select> element will be
	 * rendered with the first option from {@link $source} selected.
	 */
	protected $hasEmptyDefault = false;

	/**
	 * @var string $emptyString The title shown for an empty default selection,
	 * e.g. "Select...".
	 */
	protected $emptyString = '';

	/**
	 * Creates a new dropdown field.
	 * @param $name The field name
	 * @param $title The field title
	 * @param $source An map of the dropdown items
	 * @param $values The current values
	 * @param $form The parent form
	 * @param $emptyString mixed Add an empty selection on to of the {@link $source}-Array
	 * 	(can also be boolean, which results in an empty string)
	 *  Argument is deprecated in 2.3, please use {@link setHasEmptyDefault()} and {@link setEmptyString()} instead.
	 */
	function __construct($name, $title = null, $source = array(), $values = array(), $form = null, $emptyString = null) {
		$this->source = $source;

		if($emptyString) $this->setHasEmptyDefault(true);
		if(is_string($emptyString)) $this->setEmptyString($emptyString);
		$this->selectedValues = $values;
		parent::__construct($name, ($title===null) ? $name : $title, null, $form);
	}

    public function setValue($value, $obj = null) {
        // If we're not passed a value directly, we can look for it in a relation method on the object passed as a
        // second arg
        if(!$value && $obj && $obj instanceof DataObject && $obj->hasMethod($this->name)) {
            $funcName = $this->name;
            $value = $obj->$funcName()->getIDList();
        }

        parent::setValue($value, $obj);

        return $this;
    }

	/**
	 * @param array $properties
	 * @return string
	 */
	public function Field($properties = array()) {
		$options = '';
        if(count($this->selectedValues)== 0 && !empty($this->value)){
            $this->selectedValues = explode(',', $this->value);
        }
		$source = $this->getSource();
		if($source) {
			// For SQLMap sources, the empty string needs to be added specially
			if(is_object($source) && $this->emptyString) {
				$options .= $this->create_tag('option', array('value' => ''), $this->emptyString);
			}

			foreach($source as $value => $title) {
				$selected = null;
				// Blank value of field and source (e.g. "" => "(Any)")
				if(empty($value) && count($this->selectedValues)==0) {
					$selected = 'selected';
				} else {
					// Normal value from the source
					if($value) {
						$selected = in_array($value, $this->selectedValues)? 'selected':null;
					}
				}

				$options .= $this->create_tag(
					'option',
					array(
						'selected' => $selected,
						'value'    => $value
					),
					Convert::raw2xml($title)
				);
			}
		}

		$attrs = array(
			'class'    => ($this->extraClass() ? $this->extraClass() : ''),
			'id'       => $this->id(),
			'name'     => $this->name.'[]',
			'multiple' => 'multiple',
		);

		if($this->disabled) $attrs['disabled'] = 'disabled';
		$attrs = array_merge($attrs, $this->attributes);
		return $this->create_tag('select', $attrs, $options);
	}


	/**
	 * Gets the source array including any empty default values.
	 *
	 * @return array
	 */
	function getSource() {
		if(is_array($this->source) && $this->getHasEmptyDefault()) {
			return array(""=>$this->emptyString) + (array)$this->source;
		} else {
			return $this->source;
		}
	}

	/**
	 * @param array $source
	 */
	function setSource($source) {
		$this->source = $source;
	}

	/**
	 * @param boolean $bool
	 */
	function setHasEmptyDefault($bool) {
		$this->hasEmptyDefault = $bool;
	}

	/**
	 * @return boolean
	 */
	function getHasEmptyDefault() {
		return $this->hasEmptyDefault;
	}

	/**
	 * Set the default selection label, e.g. "select...".
	 * Defaults to an empty string. Automatically sets
	 * {@link $hasEmptyDefault} to true.
	 *
	 * @param string $str
	 */
	function setEmptyString($str) {
		$this->setHasEmptyDefault(true);
		$this->emptyString = $str;
	}

	/**
	 * @return string
	 */
	function getEmptyString() {
		return $this->emptyString;
	}

	function performReadonlyTransformation() {
		$field = new LookupField($this->name, $this->title, $this->source);
		$field->setValue($this->value);
		$field->setForm($this->form);
		$field->setReadonly(true);
		return $field;
	}

	function extraClass(){
		$ret = parent::extraClass();
		if($this->extraClass) $ret .= " $this->extraClass";
		return $ret;
	}

	/**
	 * Set form being disabled
	 */
	function setDisabled($disabled = true) {
		$this->disabled = $disabled;
	}

    public function saveInto(DataObjectInterface $record) {
        $fieldname = $this->name;
        $relation = ($fieldname && $record && $record->hasMethod($fieldname)) ? $record->$fieldname() : null;
        if($fieldname && $record && $relation &&
            ($relation instanceof RelationList || $relation instanceof UnsavedRelationList)) {
            $idList = array();
            if($this->value) foreach($this->value as $id => $bool) {
                if($bool) {
                    $idList[] = $id;
                }
            }
            $relation->setByIDList($idList);
        } elseif($fieldname && $record) {
            if($this->value) {
                $this->value = str_replace(',', '{comma}', $this->value);
                $record->$fieldname = implode(',', (array) $this->value);
            } else {
                $record->$fieldname = '';
            }
        }
    }

    /**
     * Return the CheckboxSetField value as a string
     * selected item keys.
     *
     * @return string
     */
    public function dataValue() {
        if($this->value && is_array($this->value)) {
            $filtered = array();
            foreach($this->value as $item) {
                if($item) {
                    $filtered[] = str_replace(",", "{comma}", $item);
                }
            }

            return implode(',', $filtered);
        }

        return '';
    }
}