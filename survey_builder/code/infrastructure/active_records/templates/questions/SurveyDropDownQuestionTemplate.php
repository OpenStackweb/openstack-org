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
 * Class SurveyDropDownQuestionTemplate
 */
class SurveyDropDownQuestionTemplate extends SurveyMultiValueQuestionTemplate implements IDropDownQuestionTemplate, ISurveySelectableQuestion
{

    const CountrySelectorExtra_Worldwide    =  'Worldwide';
    const CountrySelectorExtra_PreferNotSay =  'Prefer not to say';
    const CountrySelectorExtra_TooMany      =  'Too many to list';

    public static $country_selector_extra_options = [
        self::CountrySelectorExtra_PreferNotSay => self::CountrySelectorExtra_PreferNotSay,
        //self::CountrySelectorExtra_Worldwide    => self::CountrySelectorExtra_Worldwide,
        //self::CountrySelectorExtra_TooMany      => self::CountrySelectorExtra_TooMany,
    ];

    static $db = [
        'IsMultiSelect'                 => 'Boolean',
        'IsCountrySelector'             => 'Boolean',
        'UseCountrySelectorExtraOption' => 'Boolean',
        'UseChosenPlugin'               => 'Boolean',
    ];

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
        'IsMultiSelect'                 => false,
        'UseChosenPlugin'               => true,
        'IsCountrySelector'             => false,
        'UseCountrySelectorExtraOption' => false,
    );

    public function Type()
    {
        return 'ComboBox';
    }

    public function getCMSFields() {

        $fields = parent::getCMSFields();

        $fields->add(new CheckboxField('UseChosenPlugin','Use Chosen JQuery Plugin?'));

        $fields->add(new CheckboxField('IsMultiSelect','Is MultiSelect?'));

        $fields->add(new CheckboxField('IsCountrySelector','Is Country Selector?'));

        $fields->add
        (
            new CheckboxField
            (
                'UseCountrySelectorExtraOption',
                sprintf
                (
                    "Use Country Selector Extra Options (%s)?",
                    implode(',', array_keys(self::$country_selector_extra_options))
                )
            )
        );

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

       $options =  $this->UseCountrySelectorExtraOption ?
           array_merge(self::$country_selector_extra_options, CountryCodes::$iso_3166_countryCodes)
           :CountryCodes::$iso_3166_countryCodes;

       $res     = [];

       foreach($options as $k => $v)
       {
           $res[] = new ArrayData
               ([
                'ID'    => $k,
                'Label' => $v,
                'Value' => $k,
               ]);
       }

       return $res;
    }

    /**
     * @param int $id
     * @return IQuestionValueTemplate
     */
    public function getValueById($id)
    {
        $res = parent::getValueById($id);
        if($this->isCountrySelector()){

            $options =  $this->UseCountrySelectorExtraOption ?
                array_merge(self::$country_selector_extra_options, CountryCodes::$iso_3166_countryCodes)
                :CountryCodes::$iso_3166_countryCodes;

            if(isset($options[$id])) {
                $label      = $options[$id];
                $res        = new SurveyQuestionValueTemplate();
                $res->Value = $id;
                $res->Label = $label;
            }
        }
        return $res;
    }

}