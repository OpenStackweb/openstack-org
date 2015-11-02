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

class SurveyRegularStepTemplate
    extends SurveyStepTemplate
    implements ISurveyRegularStepTemplate {

    private static $db = array(
    );

    private static $indexes = array(
    );

    private static $has_one = array(
    );

    private static $belongs_to = array(
    );

    private static $many_many = array(
    );

    private static $has_many = array(
        'Questions' => 'SurveyQuestionTemplate',
    );

    private static $defaults = array(
    );

    /**
     * @return ISurveyQuestionTemplate[]
     */
    public function getQuestions()
    {
        $query = new QueryObject();
        $query->addOrder(QueryOrder::asc('Order'));
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Questions', $query)->toArray();
    }

    /**
     * @param $question_id
     * @return mixed
     */
    public function getQuestionById($question_id)
    {
        return $this->Questions()->filter('SurveyQuestionTemplate.ID', $question_id)->first();
    }

    //admin UI

    public function getCMSFields() {

        $fields = parent::getCMSFields();

        if($this->ID > 0) {
            //questions
            $config = GridFieldConfig_RecordEditor::create(100);
            $config->removeComponentsByType('GridFieldAddNewButton');
            $multi_class_selector = new GridFieldAddNewMultiClass();
            $multi_class_selector->setClasses(
                array
                (
                    'SurveyTextBoxQuestionTemplate'           => 'TextBox' ,
                    'SurveyMemberEmailQuestionTemplate'       => 'Current Member Email' ,
                    'SurveyMemberFirstNameQuestionTemplate'   => 'Current Member FirstName' ,
                    'SurveyMemberLastNameQuestionTemplate'    => 'Current Member LastName' ,
                    'SurveyTextBoxQuestionTemplate'           => 'TextBox' ,
                    'SurveyTextBoxQuestionTemplate'           => 'TextBox' ,
                    'SurveyTextAreaQuestionTemplate'          => 'TextArea',
                    'SurveyCheckBoxQuestionTemplate'          => 'CheckBox',
                    'SurveyCheckBoxListQuestionTemplate'      => 'CheckBoxList',
                    'SurveyRadioButtonListQuestionTemplate'   => 'RadioButtonList',
                    'SurveyDropDownQuestionTemplate'          => 'ComboBox',
                    'SurveyRankingQuestionTemplate'           => 'Ranking',
                    'SurveyOrganizationQuestionTemplate'      => 'Organization',
                    'SurveyLiteralContentQuestionTemplate'    => 'Literal',
                    'SurveyRadioButtonMatrixTemplateQuestion' => 'RadioButtonMatrix',
                )
            );
            $config->addComponent($multi_class_selector);
            $config->addComponent(new GridFieldSortableRows('Order'));
            $gridField = new GridField('Questions', 'Questions', $this->Questions(), $config);
            $fields->add($gridField);
        }

        return $fields;
    }

    /**
     * @param ISurveyQuestionTemplate $question
     * @return bool
     */
    public function belongsToQuestion(ISurveyQuestionTemplate $question)
    {
        foreach($this->getQuestions() as $q){
            if($q->getIdentifier() === $question->getIdentifier()) return true;
        }
        return false;
    }

    // add to end, but before thank you
    protected function onAfterWrite() {
        parent::onAfterWrite();
        $id         = $this->ID;
        if ($id === 0 || is_null($id)) return;
    }

    protected function onBeforeDelete() {
        parent::onBeforeDelete();
        foreach($this->Questions() as $q){
            $q->delete();
        }
    }

}