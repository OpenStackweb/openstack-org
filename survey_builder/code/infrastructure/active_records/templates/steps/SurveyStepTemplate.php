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

class SurveyStepTemplate
    extends DataObject
    implements ISurveyStepTemplate
{
    static $db = array
    (
        'Name'         => 'VarChar(255)',
        'Content'      => 'HTMLText',
        'FriendlyName' => 'Text',
        'Order'        => 'Int',
        'SkipStep'     => 'Boolean',
    );

    static $indexes = array
    (
        'SurveyTemplateID_Name' => array('type' => 'unique', 'value' => 'SurveyTemplateID,Name')
    );

    static $has_one = array
    (
        'SurveyTemplate' => 'SurveyTemplate'
    );

    static $belongs_to = array
    (
        'SkipStep' => false,
    );

    static $many_many = array
    (
        'DependsOn' => 'SurveyQuestionTemplate'
    );

    //Administrators Security Groups
    static $many_many_extraFields = array
    (
        'DependsOn' => array(
            'ValueID'      => "Int",
            'Operator'     => "Enum('Equal, Not-Equal','Equal')",
            'Visibility'   => "Enum('Visible, Not-Visible','Visible')",
            'BooleanOperatorOnValues' => "Enum('And, Or','And')",
        ),
    );

    static $has_many = array
    (

    );

    private static $defaults = array
    (
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    /**
     * @return ISurveyTemplate;
     */
    public function survey()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'SurveyTemplate')->getTarget();
    }

    /**
     * @return string
     */
    public function title()
    {
        return $this->getField('Name');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getField('Name');
    }

    /**
     * @return string
     */
    public function friendlyName()
    {
        return $this->getField('FriendlyName');
    }

    /**
     * @return string
     */
    public function content()
    {
        return $this->getField('Content');
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
    public function canSkip()
    {
        return $this->getField('SkipStep');
    }

    protected function onBeforeWrite() {
        parent::onBeforeWrite();
        $this->getSlug();
    }


    protected function getSlug(){
        // normalize to be url friendly
        $this->Name = UrlUtils::getSlug($this->FriendlyName);
        return $this->Name;
    }

    function getCMSValidator()
    {
        return $this->getValidator();
    }

    function getValidator()
    {
        $validator_fields  = new RequiredFields(array('FriendlyName'));
        return $validator_fields;
    }

    protected function validate()
    {
        $valid = parent::validate();
        if(!$valid->valid()) return $valid;

        if(empty($this->FriendlyName)){
            return $valid->error('Friendly Name is empty!');
        }
        $slug     = $this->getSlug();
        $id       = $this->ID;
        $owner_id = $this->SurveyTemplateID;

        $res = DB::query("SELECT COUNT(ID) FROM SurveyStepTemplate WHERE Name = '{$slug}' AND ID <> {$id} AND SurveyTemplateID = {$owner_id};")->value();
        if(intval($res) > 0 ){
            return $valid->error('There is already another step with that name!');
        }
        return $valid;
    }


    protected function onBeforeDelete() {
        parent::onBeforeDelete();
        DB::query("DELETE FROM SurveyStepTemplate_DependsOn WHERE SurveyStepTemplateID = {$this->ID};");
    }


    /**
     * @return DataList
     */
    private function getAllowedDependants(){
        $steps_query = new SQLQuery();
        $steps_query->setSelect("ID");
        $steps_query->setFrom("SurveyStepTemplate");

        $current_survey_id = $this->SurveyTemplateID;
        $high_order = $this->Order;

        $steps_query->setWhere("SurveyTemplateID = {$current_survey_id} AND `Order` <= {$high_order} ");
        $steps_query->setOrderBy('`Order`','ASC');
        $current_step_ids = $steps_query->execute()->keyedColumn();
        return SurveyQuestionTemplate::get()->filter(array ('StepID' => $current_step_ids ) );
    }

    public function getCMSFields() {

        $_REQUEST["SurveyStepTemplateID"] = $this->ID;

        $fields = new FieldList();

        $fields->add(new TextField('FriendlyName','Friendly Name'));
        $fields->add(new HtmlEditorField('Content','Content'));
        $fields->add(new CheckboxField('SkipStep','Allow To Skip'));
        $fields->add(new HiddenField('SurveyTemplateID','SurveyTemplateID'));

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
                    'DDLVisibility' => 'Visibility'
                ));

            $depends = $this->DependsOn()->sort('ID');
            $query   = $depends->dataQuery();
            $query->groupby('SurveyQuestionTemplateID');
            $depends = $depends->setDataQuery($query);

            $gridField = new GridField('DependsOn', 'Depends On Questions (Visibility)', $depends, $config);

            $fields->add($gridField);
        }

        return $fields;
    }

    protected function onAfterWrite() {
        parent::onAfterWrite();
        if (is_subclass_of(Controller::curr(), "LeftAndMain")) { // check if we are on admin (CMS side)
            //update all relationships with dependants
            foreach ($this->DependsOn() as $question) {
                if
                (
                    isset($_REQUEST["Values_{$question->ID}"]) &&
                    isset($_REQUEST["Visibility_{$question->ID}"]) &&
                    isset($_REQUEST["Operator_{$question->ID}"]) &&
                    isset($_REQUEST["BooleanOperatorOnValues_{$question->ID}"])
                ) {

                    $value_ids        = $_REQUEST["Values_{$question->ID}"];
                    $operator         = $_REQUEST["Operator_{$question->ID}"];
                    $visibility       = $_REQUEST["Visibility_{$question->ID}"];
                    $boolean_operator = $_REQUEST["BooleanOperatorOnValues_{$question->ID}"];

                    if (is_array($value_ids) && count($value_ids) > 0) {
                        DB::query("DELETE FROM SurveyStepTemplate_DependsOn WHERE SurveyStepTemplateID = {$this->ID} AND SurveyQuestionTemplateID = {$question->ID};");
                        foreach ($value_ids as $value_id) {
                            $value_id = intval(Convert::raw2sql($value_id));
                            DB::query("INSERT INTO SurveyStepTemplate_DependsOn (SurveyStepTemplateID, SurveyQuestionTemplateID , ValueID,Operator, Visibility, BooleanOperatorOnValues) VALUES ({$this->ID}, {$question->ID}, $value_id,'{$operator}','{$visibility}','{$boolean_operator}');");
                        }
                    }
                } else {
                    DB::query("DELETE FROM SurveyStepTemplate_DependsOn WHERE SurveyStepTemplateID = {$this->ID} AND SurveyQuestionTemplateID = {$question->ID};");
                    DB::query("INSERT INTO SurveyStepTemplate_DependsOn (SurveyStepTemplateID,SurveyQuestionTemplateID,Operator, Visibility, BooleanOperatorOnValues) VALUES ({$this->ID},{$question->ID},0,'Equal','Visible', 'And');");
                }
            }
        }
    }


    /**
     * @return ISurveyQuestionTemplate[]
     */
    public function getDependsOn()
    {
        $result = DB::query("
        SELECT SurveyQuestionTemplate.ClassName, SurveyQuestionTemplateID AS ID, ValueID, Operator, Visibility, BooleanOperatorOnValues
        FROM SurveyStepTemplate_DependsOn
        INNER JOIN SurveyQuestionTemplate ON SurveyQuestionTemplate.ID = SurveyQuestionTemplateID
        WHERE SurveyStepTemplateID = $this->ID
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
            $q->BooleanOperatorOnValues = $row['BooleanOperatorOnValues'];

            $list[] = $q;
        }
        return $list;
    }

}