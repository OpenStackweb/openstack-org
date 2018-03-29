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
class SurveyDoubleEntryTableQuestionTemplate
    extends SurveyMultiValueQuestionTemplate
    implements IDoubleEntryTableQuestionTemplate
{
    static $db = [
        'RowsLabel'                 => 'Text',
        'AdditionalRowsLabel'       => 'Text',
        'AdditionalRowsDescription' => 'HTMLText',
    ];

    static $has_many = [
        'Rows'           => 'SurveyQuestionRowValueTemplate',
        'Columns'        => 'SurveyQuestionColumnValueTemplate',
    ];

    /**
     * @param int $id
     * @return IQuestionValueTemplate
     */
    public function getRowById($id)
    {
        foreach($this->Rows() as $v){
            if( $v->ID === $id)
                return $v;
        }
        return null;
    }

    /**
     * @param int $id
     * @return IQuestionValueTemplate
     */
    public function getColumnById($id)
    {
        foreach($this->Columns() as $v){
            if( $v->ID === $id)
                return $v;
        }
        return null;
    }

    /**
     * @param SurveyQuestionRowValueTemplate $row
     * @return bool
     */
    public function belongToValueGroup(SurveyQuestionRowValueTemplate $row){
        foreach($this->Groups() as $group){
            if($group->hasQuestionValue($row)) return true;
        }
        return false;
    }

    public function getCMSFields() {

        $fields = parent::getCMSFields();
        $fields->add(new TextField('RowsLabel', 'Rows Label', '', 255));
        $fields->add(new TextField('AdditionalRowsLabel', 'Additional Rows Label', '', 255));
        $fields->add(new HtmlEditorField('AdditionalRowsDescription', 'Additional Rows Description'));

        if($this->ID > 0 )
        {
            $fields->removeByName('DefaultValueID');
            $fields->removeByName('Values');

            $config = GridFieldConfig_RecordEditor::create(PHP_INT_MAX);
            $config->addComponent(new GridFieldSortableRows('Order'));
            $add_button = $config->getComponentByType('GridFieldAddNewButton');
            $add_button->setButtonName('Add New Column Value');
            $gridField = new GridField('Columns', 'Columns', $this->Columns(), $config);
            $fields->add($gridField);

            $config = GridFieldConfig_RecordEditor::create(PHP_INT_MAX);
            $config->addComponent(new GridFieldSortableRows('Order'));
            $add_button = $config->getComponentByType('GridFieldAddNewButton');
            $add_button->setButtonName('Add New Row Value');
            $gridField = new GridField('Rows', 'Rows', $this->Rows(), $config);
            $fields->add($gridField);

        }

        return $fields;
    }

    public function DDLValues(){
        $selected_values = array();

        $owner = $_REQUEST["SurveyQuestionTemplateID"];

        if(isset($owner)){
            $sqlQuery = new SQLQuery();
            $sqlQuery->setSelect("ValueID");
            $sqlQuery->setFrom("SurveyQuestionTemplate_DependsOn");
            $sqlQuery->setWhere("SurveyQuestionTemplateID = {$owner} AND ChildID = {$this->ID}");
            $selected_values = $sqlQuery->execute()->keyedColumn();
        }

        return new MultiDropdownField("Values_{$this->ID}", "Values_{$this->ID}",  $this->Rows()->map("ID", "Value"), $selected_values);
    }

    /**
     * @return IQuestionValueTemplate[]
     */
    public function getColumns()
    {
        return $this->Columns()->toArray();
    }

    /**
     * @param IQuestionValueTemplate $col
     * @return $this
     * @throws Exception
     */
    public function addColumn(IQuestionValueTemplate $col)
    {
        $this->Columns()->add($col);
        return $this;
    }

    /**
     * @return IQuestionValueTemplate[]
     */
    public function getRows()
    {
        return $this->Rows()->filter('IsAdditional', 0)->sort('Order','ASC')->toArray();
    }

    public function getOrderedGroups(){
        return $this->Groups()->sort('Order','ASC');
    }

    public function getAllowedValuesForGroups(){
        return SurveyQuestionValueTemplate::get()->filter([
            'OwnerID'   => $this->ID,
            'ClassName' => 'SurveyQuestionRowValueTemplate'
        ])->leftJoin('SurveyQuestionRowValueTemplate', 'SurveyQuestionRowValueTemplate.ID = SurveyQuestionValueTemplate.ID')->where('SurveyQuestionRowValueTemplate.IsAdditional = 0');
    }
    /**
     * @param IQuestionValueTemplate $row
     * @return $this
     * @throws Exception
     */
    public function addRow(IQuestionValueTemplate $row)
    {
        $this->Rows()->add($row);
        return $this;
    }

    /**
     * @param null $excluded_ids
     * @return IQuestionValueTemplate
     */
    public function getAlternativeRows($excluded_ids = null)
    {
        $query = $this->Rows()->filter('IsAdditional', 1);

        if(!empty($excluded_ids))
            $query = $query->where(" SurveyQuestionRowValueTemplate.ID NOT IN ({$excluded_ids}) ");

        return $query->sort('Order','ASC')->toArray();
    }

    /**
     * @param int $row_id
     * @return bool
     */
    public function isAlternativeRow($row_id)
    {
        return $this->getAlternativeRow($row_id) !== null;
    }

    /**
     * @param int $row_id
     * @return IQuestionValueTemplate
     */
    public function getAlternativeRow($row_id)
    {
        return $this->Rows()->filter
        (
            array
            (
                'IsAdditional' => 1,
                'SurveyQuestionRowValueTemplate.ID' => $row_id
            )
        )->first();
    }

    /**
     * @param string $answer_value
     * @return bool
     */
    public function isValidAnswerValue($answer_value){
        return preg_match('~(\d\:\d\,?)+~', $answer_value, $match) == 1;
    }
}