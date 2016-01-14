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
class GridFieldCloneSurveyTemplateAction implements GridField_ColumnProvider, GridField_ActionProvider
{
    const ACTION_NAME = 'clonesurveytemplate';

    public function augmentColumns($gridField, &$columns) {
        if(!in_array('Actions', $columns)) {
            $columns[] = 'Actions';
        }
    }

    public function getColumnAttributes($gridField, $record, $columnName) {
        return array('class' => 'col-buttons');
    }

    public function getColumnMetadata($gridField, $columnName) {
        if($columnName == 'Actions') {
            return array('title' => '');
        }
    }

    public function getColumnsHandled($gridField) {
        return array('Actions');
    }

    public function getColumnContent($gridField, $record, $columnName) {
        $template = $gridField->getList()->byID($record->ID);
        $title    = 'Clone Template';
        $icon     = 'drive-upload';

        $field = GridField_FormAction::create($gridField,  GridFieldCloneSurveyTemplateAction::ACTION_NAME .$record->ID, false, GridFieldCloneSurveyTemplateAction::ACTION_NAME,
            array('RecordID' => $record->ID))
            ->setAttribute('title',$title)
            ->setAttribute('data-icon',$icon)
            ->setDescription($title);

        return $field->Field();
    }

    public function getActions($gridField) {
        return array(GridFieldCloneSurveyTemplateAction::ACTION_NAME);
    }

    public function handleAction(GridField $gridField, $actionName, $arguments, $data) {
        if($actionName == GridFieldCloneSurveyTemplateAction::ACTION_NAME)
        {

            $template  = $gridField->getList()->byID($arguments['RecordID']);
            $msg       = 'Survey Template Cloned !';
            $code      = 200;

            try {
                $manager = Injector::inst()->get('SurveyManager');
                $new_template = $manager->doClone($template);
            }
            catch(Exception $ex)
            {
                //SS_Log::log($ex->getMessage(). SS_Log::ERR);
                //throw new ValidationException($ex->getMessage(),0);
                //return sprintf('<div>%s</div>', $ex->getMessage());
                //Controller::curr()->getResponse()->setStatusCode($code,  $ex->getMessage());
                //Controller::curr()->getResponse()->setBody(sprintf('<div>%s</div>', $ex->getMessage()));
                throw new ValidationException($ex->getMessage() ,0);
            }

            Controller::curr()->getResponse()->setStatusCode($code,$msg);
        }
    }
}