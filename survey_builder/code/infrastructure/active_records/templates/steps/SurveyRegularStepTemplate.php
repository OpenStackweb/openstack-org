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

    static $db = array(
    );

    static $indexes = array(
    );

    static $has_one = array(
    );

    static $belongs_to = array(
    );

    static $many_many = array(
    );

    static $has_many = array(
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

    //admin UI

    public function getCMSFields() {

        $fields = new FieldList();
        $fields->add(new TextField('FriendlyName','Friendly Name'));
        $fields->add(new HtmlEditorField('Content','Content'));
        $fields->add(new CheckboxField('SkipStep','Allow To Skip'));

        if($this->ID > 0) {
            //questions
            $config = GridFieldConfig_RecordEditor::create();
            $config->removeComponentsByType('GridFieldAddNewButton');
            $multi_class_selector = new GridFieldAddNewMultiClass();
            $multi_class_selector->setClasses(
                array(
                    'SurveyTextBoxQuestionTemplate'         => 'TextBox' ,
                    'SurveyMemberEmailQuestionTemplate'     => 'Current Member Email' ,
                    'SurveyMemberFirstNameQuestionTemplate' => 'Current Member FirstName' ,
                    'SurveyMemberLastNameQuestionTemplate'  => 'Current Member LastName' ,
                    'SurveyTextBoxQuestionTemplate'      => 'TextBox' ,
                    'SurveyTextBoxQuestionTemplate'      => 'TextBox' ,
                    'SurveyTextAreaQuestionTemplate'     => 'TextArea',
                    'SurveyCheckBoxQuestionTemplate'     => 'CheckBox',
                    'SurveyCheckBoxListQuestionTemplate' => 'CheckBoxList',
                    'SurveyDropDownQuestionTemplate'     => 'ComboBox',
                    'SurveyRankingQuestionTemplate'      => 'Ranking',
                    'SurveyOrganizationQuestionTemplate' => 'Organization',
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
    public function belongsTo(ISurveyQuestionTemplate $question)
    {
        foreach($this->getQuestions() as $q){
            if($q->getIdentifier() === $question->getIdentifier()) return true;
        }
        return false;
    }

}