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

class SurveyDynamicEntityStepTemplate
    extends SurveyStepTemplate
    implements ISurveyDynamicEntityStepTemplate {

    static $db = array(
        'AddEntityText'    => 'VarChar(255)',
        'DeleteEntityText' => 'VarChar(255)',
        'EditEntityText'   => 'VarChar(255)',
    );

    static $indexes = array(

    );

    static $has_one = array(
        'EntityIcon' => 'BetterImage',
        'Entity'     => 'EntitySurveyTemplate',
    );

    static $belongs_to = array(
    );

    static $many_many = array(
    );

    static $has_many = array(
    );

    private static $defaults = array(
        'AddEntityText'    => 'Add',
        'DeleteEntityText' => 'Delete',
        'EditEntityText'   => 'Edit',
    );

    public function getCMSFields() {

        $fields = new FieldList();

        $fields->add(new TextField('FriendlyName','Friendly Name'));
        $fields->add(new HtmlEditorField('Content','Content'));
        $fields->add(new CheckboxField('SkipStep','Allow To Skip'));
        $fields->add(new TextField('AddEntityText','Add Text'));
        $fields->add(new TextField('DeleteEntityText','Delete Text'));
        $fields->add(new TextField('EditEntityText','Edit Text'));

        $icon = new UploadField('EntityIcon', 'Upload Entity Icon');
        $icon->setCanAttachExisting(false);
        $icon->setAllowedMaxFileNumber(1);
        $icon->setAllowedFileCategories('image');
        $icon->setFolderName('survey-builder');


        if($this->ID > 0) {
            $fields->add($icon);
            $id = $this->ID;
            $fields->add($ddl_entity = new DropdownField(
                'EntityID',
                'Please choose an entity to hold',
                EntitySurveyTemplate::get()->where(" (OwnerID = 0 OR OwnerID = {$id} ) ")->map("ID", "EntityName")
            ));

            $ddl_entity->setEmptyString('-- Please Select --');
        }

        return $fields;
    }

    protected function onBeforeWrite() {
        parent::onBeforeWrite();
    }

    function getCMSValidator()
    {
        return $this->getValidator();
    }

    function getValidator()
    {
        $fields = array('FriendlyName');
        if($this->ID > 0) array_push($fields, 'EntityID');
        $validator_fields  = new RequiredFields($fields);
        return $validator_fields;
    }

    protected function onAfterWrite() {
        parent::onAfterWrite();

        $id        = $this->ID;
        $entity_id = $this->EntityID;
        if($id === 0 || is_null($id) ) return;
        DB::query("UPDATE EntitySurveyTemplate SET `OwnerID` = 0 WHERE OwnerID = {$id}; ");
        if($entity_id === 0 || is_null($entity_id )) return;
        DB::query("UPDATE EntitySurveyTemplate SET `OwnerID` = {$id} WHERE ID = {$entity_id}; ");
    }

    /**
     * @return string
     */
    public function getAddEntityText()
    {
       return $this->getField('AddEntityText');
    }

    /**
     * @return string
     */
    public function getDeleteEntityText()
    {
        return $this->getField('DeleteEntityText');
    }

    /**
     * @return string
     */
    public function getEditEntityText()
    {
        return $this->getField('EditEntityText');
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'Entity')->getTarget();
    }
}