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
 * Class SurveyQuestionValueTemplate
 */
class SurveyQuestionValueTemplate
    extends DataObject
    implements IQuestionValueTemplate {

    static $db = array(
        'Value' => 'Varchar(255)',
        'Order' => 'Int',
    );

    static $has_one = array(
        'Owner' => 'SurveyMultiValueQuestionTemplate',
    );

    static $indexes = array(

    );

    static $belongs_to = array(

    );

    static $many_many = array(
    );

    static $has_many = array(
    );

    private static $defaults = array(
    );


    private static $summary_fields = array(
        'Value',
    );

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
    public function value()
    {
        return $this->getField('Value');
    }

    /**
     * @return int
     */
    public function order()
    {
        return (int)$this->getField('Order');
    }

    /**
     * @return IMultiValueQuestionTemplate
     */
    public function owner()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Owner')->getTarget();
    }

    protected function onBeforeWrite() {
        parent::onBeforeWrite();
        $this->Value = trim($this->Value);
    }

    protected function validate() {
        $valid = parent::validate();
        if(!$valid->valid()) return $valid;

        if(trim($this->Value) === ''){
            return $valid->error('Value is empty!');
        }

        return $valid;
    }

    public function getCMSFields() {
        $fields = new FieldList();
        $fields->add(new TextField('Value', 'Value'));
        $fields->add(new HiddenField('OwnerID', 'OwnerID'));
        return $fields;
    }
}