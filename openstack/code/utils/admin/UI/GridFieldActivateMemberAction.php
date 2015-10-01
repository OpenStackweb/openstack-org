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
class GridFieldActivateMemberAction implements GridField_ColumnProvider, GridField_ActionProvider
{
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
        if(!$record->canEdit()) return;
        $member = $gridField->getList()->byID($record->ID);
        $title = $member->Active? "Deactivate Member":"Activate Member";
        $icon  = $member->Active ? 'accept':'accept_disabled';

        $field = GridField_FormAction::create($gridField,  'activatemember'.$record->ID, false, "activatemember",
            array('RecordID' => $record->ID))
            ->setAttribute('title',$title)
            ->setAttribute('data-icon',$icon)
            ->setDescription($title);

        return $field->Field();
    }

    public function getActions($gridField) {
        return array('activatemember');
    }

    public function handleAction(GridField $gridField, $actionName, $arguments, $data) {
        if($actionName == 'activatemember') {
            $member = $gridField->getList()->byID($arguments['RecordID']);
            $former_state = $member->Active;
            $msg  = 'Member Activated!';
            $code = 200;
            try {
                if ($former_state) {
                    $member->Active = 0;
                } else {
                    $member->Active = 1;
                }
                $member->write();
                if($former_state){
                    $msg  = 'Member Deactivated!';
                }
            }
            catch(Exception $ex)
            {
                $code = 401;
                $msg = $ex->getMessage();
            }

            Controller::curr()->getResponse()->setStatusCode($code,$msg);
        }
    }
}