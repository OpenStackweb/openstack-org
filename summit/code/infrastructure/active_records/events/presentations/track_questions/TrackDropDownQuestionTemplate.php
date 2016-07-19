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

/**
 * Class TrackDropDownQuestionTemplate
 */
class TrackDropDownQuestionTemplate extends TrackMultiValueQuestionTemplate implements ITrackDropDownQuestionTemplate, ITrackSelectableQuestion
{

    static $db = array(
        'IsMultiSelect'     => 'Boolean',
        'IsCountrySelector' => 'Boolean',
        'UseChosenPlugin'   => 'Boolean',
    );

    static $has_one = array
    (
    );

    static $indexes = array
    (
    );

    static $belongs_to = array
    (
    );

    static $many_many = array
    (
    );

    static $has_many = array
    (
    );

    private static $defaults = array
    (
        'IsMultiSelect'     => false,
        'UseChosenPlugin'   => true,
        'IsCountrySelector' => false
    );

    public function Type()
    {
        return 'TrackDropDown';
    }

    public function getCMSFields() {

        $fields = parent::getCMSFields();

        $fields->add(new CheckboxField('UseChosenPlugin','Use Chosen JQuery Plugin?'));

        $fields->add(new CheckboxField('IsMultiSelect','Is MultiSelect?'));

        $fields->add(new CheckboxField('IsCountrySelector','Is Country Selector?'));

        return $fields;
    }

    /**
     * @return bool
     */
    public function isCountrySelector()
    {
        return $this->getField('IsCountrySelector');
    }

    /**
     * @return IQuestionValueTemplate[]
     */
    public function getValues()
    {
       if(!$this->isCountrySelector())
            return parent::getValues();

       $extra_options = array
       (
               'Worldwide' => 'Worldwide',
               'Prefer not to say' => 'Prefer not to say',
               'Too many to list' => 'Too many to list',
       );

       $options = array_merge($extra_options, CountryCodes::$iso_3166_countryCodes);
       $res = array();
       foreach($options as $k => $v)
       {
           array_push($res, new ArrayData
               (
                   array
                   (
                       'ID'    => $k,
                       'Label' => $v,
                       'Value' => $k,
                   )
               )
           );
       }
       return $res;
    }

}