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
 * Class SurveyQuestionTemplate
 */
class SurveyQuestionTemplate
    extends DataObject
    implements ISurveyQuestionTemplate
{

    static $db = array
    (
        'Name'         => 'VarChar(255)',
        'Label'        => 'HTMLText',
        'Order'        => 'Int',
        'Mandatory'    => 'Boolean',
        'ReadOnly'     => 'Boolean',
    );

    static $has_one = array
    (
        'Step' => 'SurveyStepTemplate',
    );

    static $indexes = array
    (
        'StepID_Name' => array('type' => 'unique', 'value' => 'StepID,Name')
    );

    static $belongs_to = array
    (
    );

    static $many_many = array
    (
        'DependsOn' => 'SurveyQuestionTemplate'
    );

    //Administrators Security Groups
    static $many_many_extraFields = array(
        'DependsOn' => array(
            'ValueID'      => "Int",
            'Operator'     => "Enum('Equal, Not-Equal','Equal')",
            'Visibility'   => "Enum('Visible, Not-Visible','Visible')",
            'BooleanOperatorOnValues'   => "Enum('And, Or','And')",
            'DefaultValue' => 'Varchar(254)',
        ),
    );

    static $has_many = array(

    );

    private static $defaults = array(
        'Mandatory' => true,
        'ReadOnly'  => false,
    );


    private static $summary_fields = array(
        'Type',
        'Name',
    );

    public function Type(){
        return '';
    }

    function getCMSValidator()
    {
        return $this->getValidator();
    }

    function getValidator()
    {
        $validator_fields  = new RequiredFields(array('Name','Label'));

        return $validator_fields;
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    /**
     * @return string
     */
    public function label()
    {
        return $this->getField('Label');
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->getField('Name');
    }

    /**
     * @return int
     */
    public function order()
    {
        return (int)$this->getField('Order');
    }

    /**
     * @return bool
     */
    public function isMandatory()
    {
        return $this->getField('Mandatory');
    }

    /**
     * @return bool
     */
    public function isReadOnly()
    {
        return $this->getField('ReadOnly');
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return true;
    }

    /**
     * @return ISurveyQuestionTemplate[]
     */
    public function getDependsOn()
    {
        $result = DB::query("
        SELECT SurveyQuestionTemplate.ClassName, ChildID AS ID, ValueID, Operator, Visibility, DefaultValue, BooleanOperatorOnValues
        FROM SurveyQuestionTemplate_DependsOn
        INNER JOIN SurveyQuestionTemplate ON SurveyQuestionTemplate.ID = ChildID
        WHERE SurveyQuestionTemplateID = $this->ID
        ");
        $list   = array();
        foreach($result as $row)
        {
            $class                      = $row['ClassName'];
            $question_id                = intval($row['ID']);
            $q                          = $class::get()->byID($question_id);
            $q->ValueID                 = $row['ValueID'];
            $q->Operator                = $row['Operator'];
            $q->Visibility              = $row['Visibility'];
            $q->DependantDefaultValue   = $row['DefaultValue'];
            $q->BooleanOperatorOnValues = $row['BooleanOperatorOnValues'];
            $list[] = $q;
        }
        return $list;
    }

    protected function validate()
    {
        $valid = parent::validate();
        if(!$valid->valid()) return $valid;

         if(empty($this->Name)){
            return $valid->error('Name is empty!');
        }

        if(!preg_match('/^[a-z_0-9A-Z]*$/',$this->Name)){
            return  $valid->error('Name has an Invalid Format!');
        }

        if(empty($this->Label))
        {
            return $valid->error('Label is empty!');
        }

        $survey_template_id = intval($this->Step()->SurveyTemplateID);

        $res = DB::query("SELECT COUNT(Q.ID) FROM SurveyQuestionTemplate Q
                          INNER JOIN `SurveyStepTemplate` S ON S.ID = Q.StepID
                          INNER JOIN `SurveyTemplate` T ON T.ID = S.SurveyTemplateID
                          WHERE Q.Name = '{$this->Name}' AND Q.ID <> {$this->ID} AND T.ID = {$survey_template_id};")->value();

        if(intval($res) > 0 ){
            return  $valid->error('There is already another Question on the survey with that name!');
        }

        return $valid;
    }

    public function getCMSFields() {

        $_REQUEST["SurveyQuestionTemplateID"] = $this->ID;

        $fields = new FieldList();

        $fields->add(new TextField('Name','Name (Without Spaces)'));
        $fields->add(new TextareaField('Label','Label'));
        $fields->add(new CheckboxField('Mandatory','Is Mandatory?'));
        $fields->add(new CheckboxField('ReadOnly','Is Read Only?'));

        if($this->ID > 0 ){
            //depends on
            $config = GridFieldConfig_RelationEditor::create();
            $config->removeComponentsByType('GridFieldEditButton');
            $config->removeComponentsByType('GridFieldAddNewButton');


            $config->getComponentByType('GridFieldAddExistingAutocompleter')->setSearchList($this->getAllowedDependants());

            $config->getComponentByType('GridFieldDataColumns')->setDisplayFields(
                array(
                    'Type'          => 'Type',
                    'Name'          => 'Name',
                    'DDLOperator'   => 'Operator',
                    'DDLBooleanOperator' => 'Boolean Operator (Values)',
                    'DDLValues'     => 'Values ( on which depends)',
                    'DDLVisibility' => 'Visibility',
                    'TxtValue'      => 'Default Value',
                ));

            $depends = $this->DependsOn()->sort('ID');
            $query   = $depends->dataQuery();
            $query->groupby('ChildID');
            $depends = $depends->setDataQuery($query);

            $gridField = new GridField('DependsOn', 'Depends On Questions (Visibility)', $depends, $config);

            $fields->add($gridField);
        }

        return $fields;
    }

    /**
     * @return DataList
     */
    private function getAllowedDependants()
    {
        $steps_query = new SQLQuery();
        $steps_query->setSelect("ID");
        $steps_query->setFrom("SurveyStepTemplate");

        $high_order        = $this->Step()->order();
        $current_survey_id = $this->Step()->SurveyTemplateID;

        $steps_query->setWhere("SurveyTemplateID = {$current_survey_id} AND `Order` <= {$high_order} ");
        $steps_query->setOrderBy('`Order`','ASC');
        $current_step_ids = $steps_query->execute()->keyedColumn();

        return SurveyQuestionTemplate::get()->filter(array ('StepID'=> $current_step_ids ) );
    }

    public function DDLValues(){
       return new LiteralField('Empty','&nbsp;N/A&nbsp;');
    }

    public function TxtValue(){
        return new LiteralField('Empty','&nbsp;N/A&nbsp;');
    }

    public function DDLVisibility(){
        return new LiteralField('Empty','&nbsp;N/A&nbsp;');
    }

    public function DDLOperator(){
        return new LiteralField('Empty','&nbsp;N/A&nbsp;');
    }

    public function DDLBooleanOperator()
    {
        return new LiteralField('Empty','&nbsp;N/A&nbsp;');
    }

    function onAfterWrite() {
        parent::onAfterWrite();
        if (is_subclass_of(Controller::curr(), "LeftAndMain")) { // check if we are on admin (CMS side)
            //update all relationships with dependants
            foreach ($this->DependsOn() as $question) {
                if
                (
                    isset($_REQUEST["Values_{$question->ID}"]) &&
                    isset($_REQUEST["Visibility_{$question->ID}"]) &&
                    isset($_REQUEST["DefaultValue_{$question->ID}"]) &&
                    isset($_REQUEST["Operator_{$question->ID}"]) &&
                    isset($_REQUEST["BooleanOperatorOnValues_{$question->ID}"])
                )
                {

                    $value_ids     = $_REQUEST["Values_{$question->ID}"];
                    $operator      = $_REQUEST["Operator_{$question->ID}"];
                    $visibility    = $_REQUEST["Visibility_{$question->ID}"];
                    $initial_value = $_REQUEST["DefaultValue_{$question->ID}"];
                    $boolean_operator = $_REQUEST["BooleanOperatorOnValues_{$question->ID}"];

                    if (is_array($value_ids) && count($value_ids) > 0) {
                        DB::query("DELETE FROM SurveyQuestionTemplate_DependsOn WHERE SurveyQuestionTemplateID = {$this->ID} AND ChildID = {$question->ID};");
                        foreach ($value_ids as $value_id) {
                            $value_id = intval(Convert::raw2sql($value_id));
                            DB::query("INSERT INTO SurveyQuestionTemplate_DependsOn (SurveyQuestionTemplateID, ChildID , ValueID,Operator, Visibility, DefaultValue, BooleanOperatorOnValues) VALUES ({$this->ID}, {$question->ID}, $value_id,'{$operator}','{$visibility}','{$initial_value}', '{$boolean_operator}');");
                        }
                    }
                } else {
                    DB::query("DELETE FROM SurveyQuestionTemplate_DependsOn WHERE SurveyQuestionTemplateID = {$this->ID} AND ChildID = {$question->ID};");
                    DB::query("INSERT INTO SurveyQuestionTemplate_DependsOn (SurveyQuestionTemplateID,ChildID,ValueID,Operator, Visibility,DefaultValue, BooleanOperatorOnValues) VALUES ({$this->ID},{$question->ID},0,'Equal','Visible','','And');");
                }
            }
        }
    }

    protected function onBeforeDelete()
    {
        parent::onBeforeDelete();
        DB::query("DELETE FROM SurveyQuestionTemplate_DependsOn WHERE SurveyQuestionTemplateID = {$this->ID};");
    }

    /**
     * @return ISurveyStepTemplate
     */
    public function step()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this,'Step')->getTarget();
    }
}