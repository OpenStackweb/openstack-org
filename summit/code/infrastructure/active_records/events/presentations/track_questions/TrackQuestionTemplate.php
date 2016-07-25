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
 * Class TrackQuestionTemplate
 */
class TrackQuestionTemplate
    extends DataObject
    implements ITrackQuestionTemplate
{

    static $db = array
    (
        'Name'                    => 'VarChar(255)',
        'Label'                   => 'HTMLText',
        'Mandatory'               => 'Boolean',
        'ReadOnly'                => 'Boolean',
        'AfterQuestion'           => "Enum('Title,CategoryContainer,LevelProblemAddressed,AttendeesExpectedLearnt,Last','Last')",
    );

    static $has_one = array();

    static $belongs_many_many = array
    (
        'Tracks' => 'PresentationCategory'
    );

    static $has_many = array
    (
        'Answers' => 'TrackAnswer'
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

        $res = DB::query("SELECT COUNT(Q.ID) FROM TrackQuestionTemplate Q
                          WHERE Q.Name = '{$this->Name}' AND Q.ID <> {$this->ID}")->value();

        if (intval($res) > 0) {
                return $valid->error('There is already another Question with that name! Try linking existing an question.');
        }

        return $valid;
    }

    public function getCMSFields() {

        $_REQUEST["TrackID"] = $this->ID;

        $fields = new FieldList();

        $fields->add(new TextField('Name','Name (Without Spaces)'));
        $fields->add(new TextareaField('Label','Label'));
        $fields->add(new CheckboxField('Mandatory','Is Mandatory?'));
        $fields->add(new CheckboxField('ReadOnly','Is Read Only?'));
        //$fields->add(new DropdownField('AfterQuestion','Insert After Question',$this->dbObject('AfterQuestion')->enumValues()));

        return $fields;
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

}