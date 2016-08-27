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

class TrackMultiValueQuestionTemplate
    extends TrackQuestionTemplate
    implements ITrackMultiValueQuestionTemplate {

    static $db = array
    (
        'EmptyString' => 'VarChar(255)'
    );

    static $has_one = array
    (
        'DefaultValue' => 'TrackQuestionValueTemplate',
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
        'Values' => 'TrackQuestionValueTemplate'
    );

    private static $defaults = array
    (
        'EmptyString' => '-- Select One --'
    );

    public function getCMSFields() {

        $fields = parent::getCMSFields();

        $fields->add(new TextField('EmptyString', 'Empty String', '', 255));

        if($this->ID > 0 ){
            //validation rules
            $config = GridFieldConfig_RecordEditor::create();
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
        }

        return $fields;
    }

    public function DDLValues(){
        $selected_values = array();

        if(isset($_REQUEST["TrackQuestionTemplateID"])) {
            $owner = $_REQUEST["TrackQuestionTemplateID"];

            if (isset($owner)) {
                $sqlQuery = new SQLQuery();
                $sqlQuery->setSelect("ValueID");
                $sqlQuery->setFrom("TrackQuestionTemplate_DependsOn");
                $sqlQuery->setWhere("TrackQuestionTemplateID = {$owner} AND ChildID = {$this->ID}");
                $selected_values = $sqlQuery->execute()->keyedColumn();
            }
        }
        return new MultiDropdownField("Values_{$this->ID}", "Values_{$this->ID}",  $this->Values()->map("ID", "Value"), $selected_values);
    }

    public function TxtValue(){
        $value = '';

        $owner = $_REQUEST["TrackQuestionTemplateID"];

        if(isset($owner)){
            $sqlQuery = new SQLQuery();
            $sqlQuery->setSelect("DefaultValue");
            $sqlQuery->setFrom("TrackQuestionTemplate_DependsOn");
            $sqlQuery->setWhere("TrackQuestionTemplateID = {$owner} AND ChildID = {$this->ID}");
            $value = current($sqlQuery->execute()->keyedColumn());
        }

       return new TextField("DefaultValue_{$this->ID}", "DefaultValue_{$this->ID}", $value, 254);
    }

    public function DDLOperator(){

        $values = array('Equal' => 'Equal', 'Not-Equal' => 'Not-Equal');
        $selected_value = '';

        if(isset($_REQUEST["TrackQuestionTemplateID"]))
        {
            $owner = $_REQUEST["TrackQuestionTemplateID"];
            if (isset($owner)) {
                $sqlQuery = new SQLQuery();
                $sqlQuery->setSelect("Operator");
                $sqlQuery->setFrom("TrackQuestionTemplate_DependsOn");
                $sqlQuery->setWhere("TrackQuestionTemplateID = {$owner} AND ChildID = {$this->ID}");
                $selected_value = current($sqlQuery->execute()->keyedColumn());
            }

        }
        return new DropdownField("Operator_{$this->ID}", "Operator_{$this->ID}", $values, $selected_value);
    }


    public function DDLBooleanOperator(){

        $values = array('And' => 'And', 'Or' => 'Or');
        $selected_value = '';

        if(isset($_REQUEST["TrackQuestionTemplateID"]))
        {
            $owner = $_REQUEST["TrackQuestionTemplateID"];
            if (isset($owner)) {
                $sqlQuery = new SQLQuery();
                $sqlQuery->setSelect("BooleanOperatorOnValues");
                $sqlQuery->setFrom("TrackQuestionTemplate_DependsOn");
                $sqlQuery->setWhere("TrackQuestionTemplateID = {$owner} AND ChildID = {$this->ID}");
                $selected_value = current($sqlQuery->execute()->keyedColumn());
            }

        }
        return new DropdownField("BooleanOperatorOnValues_{$this->ID}", "BooleanOperatorOnValues_{$this->ID}", $values, $selected_value);
    }


    public function DDLVisibility(){

        $values = array('Visible' => 'Visible', 'Not-Visible' => 'Not-Visible');
        $selected_value = '';

        if(isset($_REQUEST["TrackQuestionTemplateID"])) {
            $owner = $_REQUEST["TrackQuestionTemplateID"];

            if (isset($owner)) {
                $sqlQuery = new SQLQuery();
                $sqlQuery->setSelect("Visibility");
                $sqlQuery->setFrom("TrackQuestionTemplate_DependsOn");
                $sqlQuery->setWhere("TrackQuestionTemplateID = {$owner} AND ChildID = {$this->ID}");
                $selected_value = current($sqlQuery->execute()->keyedColumn());
            }
        }

        return new DropdownField("Visibility_{$this->ID}", "Visibility_{$this->ID}", $values, $selected_value);
    }

    /**
     * @return ITrackQuestionValueTemplate[]
     */
    public function getValues()
    {
        $query = new QueryObject();
        $query->addOrder(QueryOrder::asc('Order'));
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Values', $query)->toArray();
    }

    /**
     * @param ITrackQuestionValueTemplate $value
     * @return $this
     */
    public function addValue(ITrackQuestionValueTemplate $value)
    {
        $query = new QueryObject();
        $query->addOrder(QueryOrder::asc('Order'));
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Values', $query)->add($value);
        return $this;
    }

    public function getFormattedValues()
    {
        return new ArrayList($this->getValues());
    }

    /**
     * @return ITrackQuestionValueTemplate
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
            if( $v->getIdentifier() === $id)
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
     * @return ITrackQuestionValueTemplate
     */
    public function getValueByValue($value)
    {
        foreach($this->values() as $v){
            if( $v->value() === $value)
                return $v;
        }
        return null;
    }

}