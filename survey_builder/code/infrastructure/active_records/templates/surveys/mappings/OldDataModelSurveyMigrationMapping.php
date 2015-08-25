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
class OldDataModelSurveyMigrationMapping extends AbstractSurveyMigrationMapping implements IOldSurveyMigrationMapping
{
    static $db = array
    (
        'OriginTable' => 'Text',
        'OriginField' => 'Text'
    );

    static $indexes = array(

    );

    static $has_one = array
    (

    );

    static $belongs_to = array
    (
    );

    static $many_many = array(
    );

    static $has_many = array(
    );

    private static $defaults = array(
    );

    private static $summary_fields = array(
        'OriginTable',
        'OriginField',
        'TargetField.Name'
    );

    public function getCMSFields()
    {
        $field = parent::getCMSFields();

        $origin_table =  array
        (
            'DeploymentSurvey' => 'DeploymentSurvey',
            'AppDevSurvey'     => 'AppDevSurvey',
            'Deployment'       => 'Deployment',
        );

        if(isset($_REQUEST['entity_survey']))
        {
            unset($origin_table['AppDevSurvey']);
            unset($origin_table['DeploymentSurvey']);
        }

        $field->addFieldToTab('Root.Main', $ddl_origin_table = new DropdownField('OriginTable', 'OriginTable', $origin_table));

        $ddl_origin_table->setEmptyString('-- select an origin table --');

        $source = array();

        if($this->ID > 0)
        {
                switch($this->OriginTable)
                {
                    case 'DeploymentSurvey':
                    {
                        $source = DeploymentSurveyFields::$fields;
                    }
                    break;
                    case 'AppDevSurvey':
                    {
                        $source = AppDevSurveyFields::$fields;
                    }
                    break;
                    case 'Deployment':
                    {
                        $source = DeploymentFields::$fields;
                    }
                    break;
                }
        }

        $field->addFieldToTab('Root.Main',$ddl_origin_field = new DropdownField('OriginField', 'OriginField', $source));

        $ddl_origin_field->setEmptyString('-- select an origin field --');

        return $field;
    }

    /**
     * @return string
     */
    public function getOriginTableName()
    {
        return $this->OriginTable;
    }

    /**
     * @return string
     */
    public function getOriginFieldName()
    {
        return $this->OriginField;
    }
}