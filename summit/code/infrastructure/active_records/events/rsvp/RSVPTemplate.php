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

class RSVPTemplate extends DataObject implements IRSVPTemplate {

    static $db = array
    (
        'Title'     => 'VarChar(255)',
        'Enabled'   => 'Boolean',
    );

    static $indexes = array ();

    static $has_one = array
    (
        'CreatedBy' => 'Member',
    );

    static $belongs_to = array();

    static $many_many = array();

    static $has_many = array
    (
        'Questions'  => 'RSVPQuestionTemplate',
        'Event'      => 'SummitEvent',
    );

    private static $defaults = array
    (
    );

    public function getCMSFields()
    {

        $fields = new FieldList(
            $rootTab = new TabSet("Root",   $tabMain = new Tab('Main'))
        );

        $fields->addFieldToTab('Root.Main',new TextField('Title','Title'));
        $fields->addFieldToTab('Root.Main',new CheckboxField('Enabled','Is Enabled'));
        $fields->addFieldToTab('Root.Main',new HiddenField('CreatedByID','CreatedByID', Member::currentUserID()));

        //questions
        if($this->ID > 0)
        {
            $_REQUEST['rsvp_template_id'] = $this->ID;
            // steps
            $config = GridFieldConfig_RecordEditor::create();
            $config->removeComponentsByType('GridFieldAddNewButton');
            $multi_class_selector = new GridFieldAddNewMultiClass();

            $multi_class_selector->setClasses(
                array
                (
                    'RSVPMemberEmailQuestionTemplate'       => 'Current Member Email' ,
                    'RSVPMemberFirstNameQuestionTemplate'   => 'Current Member FirstName' ,
                    'RSVPMemberLastNameQuestionTemplate'    => 'Current Member LastName' ,
                    'RSVPTextBoxQuestionTemplate'           => 'TextBox' ,
                    'RSVPTextAreaQuestionTemplate'          => 'TextArea',
                    'RSVPCheckBoxQuestionTemplate'          => 'CheckBox',
                    'RSVPCheckBoxListQuestionTemplate'      => 'CheckBoxList',
                    'RSVPRadioButtonListQuestionTemplate'   => 'RadioButtonList',
                    'RSVPDropDownQuestionTemplate'          => 'ComboBox',
                    'RSVPLiteralContentQuestionTemplate'    => 'Literal',
                )
            );
            $config->addComponent($multi_class_selector);
            $config->addComponent(new GridFieldSortableRows('Order'));
            $gridField = new GridField('Questions', 'Questions', $this->Questions(), $config);
            $fields->add($gridField);


        }

        return $fields;
    }

    function getCMSValidator()
    {
        return $this->getValidator();
    }

    function getValidator()
    {
        $validator_fields  = new RequiredFields(array('Title'));

        return $validator_fields;
    }

    protected function onBeforeDelete()
    {
        parent::onBeforeDelete();
        foreach($this->Questions() as $q){
            $q->delete();
        }
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->getField('Enabled');
    }

    /**
     * @return IFoundationMember
     */
    public function owner()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'CreatedBy')->getTarget();
    }

    /**
     * @return IRSVPQuestionTemplate[]
     */
    public function getQuestions()
    {
        $query = new QueryObject();
        $query->addOrder(QueryOrder::asc('Order'));
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Questions', $query)->toArray();
    }

    /**
     * @param IRSVPQuestionTemplate $question
     * @return void
     */
    public function addQuestion(IRSVPQuestionTemplate $question)
    {
        $query = new QueryObject();
        $query->addOrder(QueryOrder::asc('Order'));
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Questions', $query)->add($question);
    }

    protected function validate()
    {
        $valid = parent::validate();
        if(!$valid->valid()) return $valid;

        if(empty($this->Title)){
            return $valid->error('Friendly Name is empty!');
        }
        $title = $this->Title;
        $id    = $this->ID;

        $res = DB::query("SELECT COUNT(ID) FROM RSVPTemplate WHERE Title = '{$title}' AND ID <> {$id}")->value();
        if(intval($res) > 0 )
        {
            return $valid->error('There is already another rsvp template with that name!');
        }

        return $valid;
    }

    /**
     * @param $question_id
     * @return IRSVPQuestionTemplate
     */
    public function getQuestionById($question_id){
        return $this->Questions()->filter('RSVPQuestionTemplate.ID', $question_id)->first();
    }

}