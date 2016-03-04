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
class EntitySurveyTemplate extends SurveyTemplate implements IEntitySurveyTemplate {


    static $db = array
    (
        'EntityName'     => 'VarChar(255)',
        'UseTeamEdition' => 'Boolean',
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

        $_REQUEST['entity_survey'] = 1;

        $fields->add(new TextField('EntityName','Entity Name (Without Spaces)'));
        $fields->add(new CheckboxField('Enabled','Is Enabled?'));
        $fields->add(new CheckboxField('UseTeamEdition', 'Allow Team Edition?'));
        $fields->add(new HiddenField('CreatedByID','CreatedByID', Member::currentUserID()));
        $fields->add(new HiddenField('ParentID','ParentID'));
        //steps
        if($this->ID > 0) {
            $_REQUEST['survey_template_id'] = $this->ID;
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

            $config    = GridFieldConfig_RecordEditor::create();
            $config->removeComponentsByType('GridFieldAddNewButton');
            $multi_class_selector = new GridFieldAddNewMultiClass();

            $migration_mapping_types = array
            (
                //'OldDataModelSurveyMigrationMapping' => 'Old Survey Data Mapping' ,
                'NewDataModelSurveyMigrationMapping' => 'New Migration Mapping'
            );


            $multi_class_selector->setClasses
            (
                $migration_mapping_types
            );

            $config->addComponent($multi_class_selector);
            $gridField = new GridField('MigrationMappings', 'Migration Mappings', $this->MigrationMappings(), $config);

            $dataColumns = $config->getComponentByType('GridFieldDataColumns');
            $migration   = $this->MigrationMappings()->first();

            $dataColumns->setDisplayFields(!is_null($migration) && $migration->ClassName === 'OldDataModelSurveyMigrationMapping' ?
                OldDataModelSurveyMigrationMapping::getDisplayFields():
                NewDataModelSurveyMigrationMapping::getDisplayFields());

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

    protected function validate() {
        $valid = ValidationResult::create();
        if(empty($this->EntityName)){
            return $valid->error('Entity Name is empty!');
        }

        if(!preg_match('/^[a-z_091A-Z]*$/',$this->EntityName)){
            return  $valid->error('Entity Name has an Invalid Format!');
        }

        $title    = $this->EntityName;
        $id       = $this->ID;
        $owner_id = $this->ParentID;
        if($owner_id > 0) {
            $res = DB::query("SELECT COUNT(ID) FROM EntitySurveyTemplate WHERE EntityName = '{$title}' AND ID <> {$id} AND ParentID = {$owner_id}")->value();
            if (intval($res) > 0) {
                return $valid->error('There is already another entity survey with that name!');
            }
        }
        return $valid;
    }

    protected function onBeforeDelete() {
        parent::onBeforeDelete();
    }

    /**
     * @return bool
     */
    public function belongsToDynamicStep()
    {
        $owner_step = $this->getDynamicStepTemplate();
        return !is_null($owner_step);
    }

    /**
     * @return ISurveyDynamicEntityStepTemplate
     */
    public function getDynamicStepTemplate()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Owner')->getTarget();
    }

    /**
     * @return string
     */
    public function QualifiedName()
    {
        return sprintf("%s - %s", $this->Parent()->Title, $this->Title);
    }
}