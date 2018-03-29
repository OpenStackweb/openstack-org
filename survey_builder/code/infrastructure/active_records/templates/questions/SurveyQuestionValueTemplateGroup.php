<?php
/**
 * Copyright 2017 OpenStack Foundation
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
 * Class SurveyQuestionValueTemplateGroup
 */
class SurveyQuestionValueTemplateGroup extends DataObject
{

    static $db = [
        'Label' => 'HTMLText',
        'Order' => 'Int',
    ];

    static $has_one = [
        'Owner' => 'SurveyMultiValueQuestionTemplate',
    ];

    static $has_many = [
        'Values' => 'SurveyQuestionValueTemplate',
    ];

    private static $summary_fields = [
        'ID'             => 'Id',
        'LabelNice'      => 'Label',
        'ValuesNiceList' => 'Values'
    ];

    public function getOrderedValues(){
        return $this->Values()->sort('Order','ASC');
    }

    public function LabelNice(){
        return strip_tags($this->Label);
    }

    /**
     * @return string
     */
    public function ValuesNiceList(){
        $res = [];
        foreach($this->getOrderedValues() as $val){
            $res[] = $val->Value;
        }
        return implode(', ', $res);
    }

    public function getCMSFields() {

        $fields = new FieldList();
        $fields->add(new HtmlEditorField('Label','Label'));
        $fields->add(new HiddenField('OwnerID','OwnerID'));

        if($this->ID > 0 ){
            $config = GridFieldConfig_RelationEditor::create(PHP_INT_MAX);
            $config->removeComponentsByType('GridFieldAddNewButton');
            $config->removeComponentsByType('GridFieldEditButton');
            $config->addComponent(new GridFieldSortableRows('Order'));
            $config->getComponentByType('GridFieldAddExistingAutocompleter')->setSearchList(
                $this->Owner()->getAllowedValuesForGroups()
            );
            $config->getComponentByType('GridFieldAddExistingAutocompleter')->setResultsFormat('$Label ($ID)');
            $gridField = new GridField('Values', 'Values', $this->Values(), $config);
            $fields->add($gridField);
        }

        return $fields;
    }

    protected function validate()
    {
        $valid = parent::validate();
        if(!$valid->valid()) return $valid;

        if(empty($this->Label)){
            return $valid->error('Label is empty!');
        }

        return $valid;
    }

    /**
     * @param SurveyQuestionValueTemplate $value
     * @return bool
     */
    public function hasQuestionValue(SurveyQuestionValueTemplate $value){
        return intval($this->Values()->filter(['ID' => $value->ID])->count()) > 0;
    }

}