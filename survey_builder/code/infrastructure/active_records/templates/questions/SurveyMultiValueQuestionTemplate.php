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

class SurveyMultiValueQuestionTemplate
    extends SurveyQuestionTemplate
    implements IMultiValueQuestionTemplate {

    static $db = [
        'EmptyString'       => 'VarChar(255)',
        'DefaultGroupLabel' => 'HTMLText',
    ];

    static $has_one = [
        'DefaultValue' => 'SurveyQuestionValueTemplate',
    ];

    static $indexes = [];

    static $belongs_to = [];

    static $many_many = [];

    static $has_many = [
        'Values' => 'SurveyQuestionValueTemplate',
        'Groups' => 'SurveyQuestionValueTemplateGroup'
    ];

    private static $defaults = [
        'EmptyString' => '-- Select One --'
    ];

    /**
     * @return mixed|string
     */
    public function getDefaultGroupLabel(){
        $val = $this->getField('DefaultGroupLabel');
        return empty($val) ? '<p>Others</p>' : $val;
    }

    public function getCMSFields() {

        $fields = parent::getCMSFields();

        $fields->add(new TextField('EmptyString', 'Empty String', '', 255));

        if($this->ID > 0 ){
            //validation rules
            $config = GridFieldConfig_RecordEditor::create(PHP_INT_MAX);
            $config->addComponent(new GridFieldSortableRows('Order'));
            $add_button = $config->getComponentByType('GridFieldAddNewButton');
            $add_button->setButtonName('Add New Value');
            $gridField = new GridField('Values', 'Values', $this->Values(), $config);
            $fields->add($gridField);
            if($this->Values()->count() > 0){

                $fields->add($ddl_default = new DropdownField(
                    'DefaultValueID',
                    'Please choose an default value',
                    $this->Values()->map("ID", "Value")
                ));
                $ddl_default->setEmptyString('-- select --');
            }

            //
            $fields->add(new HtmlEditorField("DefaultGroupLabel", "Default Group Label <small>( this group will include all values without group assigned)</small>") );
            $config = GridFieldConfig_RecordEditor::create(PHP_INT_MAX);
            $config->addComponent(new GridFieldSortableRows('Order'));
            $gridField = new GridField('Groups', 'Values Groups', $this->Groups(), $config);
            $add_button = $config->getComponentByType('GridFieldAddNewButton');
            $add_button->setButtonName('Add New Values Group');
            $config->getComponentByType('GridFieldDataColumns')->setDisplayFields(
                [
                    'ID'    => 'ID',
                    'LabelNice' => 'Label',
                    'ValuesNiceList' => 'Values',
                ]
            );

            $fields->add($gridField);
        }

        return $fields;
    }

    public function JSONValues(){
        $values = [];
        foreach($this->getValues() as $value){
            $values[] = [
                'id'    => $value->ID,
                'label' => $value->Value,
            ];
        }
        return json_encode($values);
    }

    public function DDLValues(){
        $selected_values = array();

        if(isset($_REQUEST["SurveyQuestionTemplateID"])) {
            $owner = $_REQUEST["SurveyQuestionTemplateID"];

            if (isset($owner)) {
                $sqlQuery = new SQLQuery();
                $sqlQuery->setSelect("ValueID");
                $sqlQuery->setFrom("SurveyQuestionTemplate_DependsOn");
                $sqlQuery->setWhere("SurveyQuestionTemplateID = {$owner} AND ChildID = {$this->ID}");
                $selected_values = $sqlQuery->execute()->keyedColumn();
            }
        }
        else if(isset($_REQUEST["SurveyStepTemplateID"]))
        {
            $owner = $_REQUEST["SurveyStepTemplateID"];
            if (isset($owner)) {
                $sqlQuery = new SQLQuery();
                $sqlQuery->setSelect("ValueID");
                $sqlQuery->setFrom("SurveyStepTemplate_DependsOn");
                $sqlQuery->setWhere("SurveyStepTemplateID = {$owner} AND SurveyQuestionTemplateID = {$this->ID}");
                $selected_values = $sqlQuery->execute()->keyedColumn();
            }
        }
        return new MultiDropdownField("Values_{$this->ID}", "Values_{$this->ID}",  $this->Values()->map("ID", "Value"), $selected_values);
    }

    public function TxtValue(){
        $value = '';

        $owner = $_REQUEST["SurveyQuestionTemplateID"];

        if(isset($owner)){
            $sqlQuery = new SQLQuery();
            $sqlQuery->setSelect("DefaultValue");
            $sqlQuery->setFrom("SurveyQuestionTemplate_DependsOn");
            $sqlQuery->setWhere("SurveyQuestionTemplateID = {$owner} AND ChildID = {$this->ID}");
            $value = current($sqlQuery->execute()->keyedColumn());
        }

       return new TextField("DefaultValue_{$this->ID}", "DefaultValue_{$this->ID}", $value, 254);
    }

    public function DDLOperator(){

        $values = array('Equal' => 'Equal', 'Not-Equal' => 'Not-Equal');
        $selected_value = '';

        if(isset($_REQUEST["SurveyQuestionTemplateID"]))
        {
            $owner = $_REQUEST["SurveyQuestionTemplateID"];
            if (isset($owner)) {
                $sqlQuery = new SQLQuery();
                $sqlQuery->setSelect("Operator");
                $sqlQuery->setFrom("SurveyQuestionTemplate_DependsOn");
                $sqlQuery->setWhere("SurveyQuestionTemplateID = {$owner} AND ChildID = {$this->ID}");
                $selected_value = current($sqlQuery->execute()->keyedColumn());
            }

        }
        else if(isset($_REQUEST["SurveyStepTemplateID"]))
        {
            $owner = $_REQUEST["SurveyStepTemplateID"];
            if (isset($owner)) {
                $sqlQuery = new SQLQuery();
                $sqlQuery->setSelect("Operator");
                $sqlQuery->setFrom("SurveyStepTemplate_DependsOn");
                $sqlQuery->setWhere("SurveyStepTemplateID = {$owner} AND SurveyQuestionTemplateID = {$this->ID}");
                $selected_value = current($sqlQuery->execute()->keyedColumn());
            }
        }
        return new DropdownField("Operator_{$this->ID}", "Operator_{$this->ID}", $values, $selected_value);
    }


    public function DDLBooleanOperator(){

        $values = array('And' => 'And', 'Or' => 'Or');
        $selected_value = '';

        if(isset($_REQUEST["SurveyQuestionTemplateID"]))
        {
            $owner = $_REQUEST["SurveyQuestionTemplateID"];
            if (isset($owner)) {
                $sqlQuery = new SQLQuery();
                $sqlQuery->setSelect("BooleanOperatorOnValues");
                $sqlQuery->setFrom("SurveyQuestionTemplate_DependsOn");
                $sqlQuery->setWhere("SurveyQuestionTemplateID = {$owner} AND ChildID = {$this->ID}");
                $selected_value = current($sqlQuery->execute()->keyedColumn());
            }

        }
        else if(isset($_REQUEST["SurveyStepTemplateID"]))
        {
            $owner = $_REQUEST["SurveyStepTemplateID"];
            if (isset($owner)) {
                $sqlQuery = new SQLQuery();
                $sqlQuery->setSelect("BooleanOperatorOnValues");
                $sqlQuery->setFrom("SurveyStepTemplate_DependsOn");
                $sqlQuery->setWhere("SurveyStepTemplateID = {$owner} AND SurveyQuestionTemplateID = {$this->ID}");
                $selected_value = current($sqlQuery->execute()->keyedColumn());
            }
        }
        return new DropdownField("BooleanOperatorOnValues_{$this->ID}", "BooleanOperatorOnValues_{$this->ID}", $values, $selected_value);
    }


    public function DDLVisibility(){

        $values = array('Visible' => 'Visible', 'Not-Visible' => 'Not-Visible');
        $selected_value = '';

        if(isset($_REQUEST["SurveyQuestionTemplateID"])) {
            $owner = $_REQUEST["SurveyQuestionTemplateID"];

            if (isset($owner)) {
                $sqlQuery = new SQLQuery();
                $sqlQuery->setSelect("Visibility");
                $sqlQuery->setFrom("SurveyQuestionTemplate_DependsOn");
                $sqlQuery->setWhere("SurveyQuestionTemplateID = {$owner} AND ChildID = {$this->ID}");
                $selected_value = current($sqlQuery->execute()->keyedColumn());
            }
        }
        else if(isset($_REQUEST["SurveyStepTemplateID"])) {
            $owner = $_REQUEST["SurveyStepTemplateID"];

            if (isset($owner)) {
                $sqlQuery = new SQLQuery();
                $sqlQuery->setSelect("Visibility");
                $sqlQuery->setFrom("SurveyStepTemplate_DependsOn");
                $sqlQuery->setWhere("SurveyStepTemplateID = {$owner} AND SurveyQuestionTemplateID = {$this->ID}");
                $selected_value = current($sqlQuery->execute()->keyedColumn());
            }
        }

        return new DropdownField("Visibility_{$this->ID}", "Visibility_{$this->ID}", $values, $selected_value);
    }

    /**
     * @return IQuestionValueTemplate[]
     */
    public function getValues()
    {
        return $this->Values()->sort('Order','ASC')->toArray();
    }

    /**
     * @param IQuestionValueTemplate $value
     * @return $this
     */
    public function addValue(IQuestionValueTemplate $value)
    {
        $this->Values()->add($value);
        return $this;
    }

    public function getFormattedValues()
    {
        return new ArrayList($this->getValues());
    }

    /**
     * @return IQuestionValueTemplate
     */
    public function getDefaultValue()
    {
        return $this->DefaultValue();
    }

    /**
     * @param int $id
     * @return IQuestionValueTemplate
     */
    public function getValueById($id)
    {
        foreach($this->values() as $v){
            if( $v->getIdentifier() === intval($id))
                return $v;
        }
        return null;
    }

    protected function onBeforeDelete() {
        parent::onBeforeDelete();
        foreach($this->Values() as $v){
            $v->delete();
        }
    }

    /**
     * @param string $value
     * @return IQuestionValueTemplate
     */
    public function getValueByValue($value)
    {
        foreach($this->values() as $v){
            if( $v->value() === $value)
                return $v;
        }
        return null;
    }

    public function getAllowedValuesForGroups(){
        return SurveyQuestionValueTemplate::get()->filter([
            'OwnerID' => $this->ID,
        ]);
    }

}