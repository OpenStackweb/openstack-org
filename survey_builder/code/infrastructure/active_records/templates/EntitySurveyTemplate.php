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
 * Class EntitySurveyTemplate
 */
class EntitySurveyTemplate
    extends SurveyTemplate
    implements IEntitySurveyTemplate {


    static $db = array(
        'EntityName'  => 'VarChar(255)',
    );

    static $indexes = array(
        'ParentID_EntityName' => array('type'=>'unique', 'value'=>'ParentID,EntityName')
    );

    static $has_one = array(
        'Parent' => 'SurveyTemplate',
        'Owner'  => 'SurveyDynamicEntityStepTemplate',
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
        'EntityName',
    );

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getField('EntityName');
    }

    public function getCMSFields() {

        $fields = new FieldList();

        $fields->add(new TextField('EntityName','Entity Name'));
        $fields->add(new CheckboxField('Enabled','Is Enabled'));
        $fields->add(new HiddenField('CreatedByID','CreatedByID', Member::currentUserID()));

        //steps
        if($this->ID > 0) {
            // steps
            $config = GridFieldConfig_RecordEditor::create();
            $config->removeComponentsByType('GridFieldAddNewButton');
            $multi_class_selector = new GridFieldAddNewMultiClass();

            $multi_class_selector->setClasses( array(
                    'SurveyRegularStepTemplate' => 'Regular Step' ,
                )
            );

            $config->addComponent($multi_class_selector);
            $config->addComponent(new GridFieldSortableRows('Order'));
            $gridField = new GridField('Steps', 'Steps', $this->Steps(), $config);
            $fields->add($gridField);
        }
        return $fields;
    }

    protected function onBeforeWrite() {
        parent::onBeforeWrite();
        $this->Name = $this->EntityName;
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return $this->getField('EntityName');
    }
}