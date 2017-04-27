<?php

/**
 * Copyright 2016 OpenStack Foundation
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
class GridFieldApprovePushNotificationAction implements GridField_ColumnProvider, GridField_ActionProvider
{
    public function augmentColumns($gridField, &$columns)
    {
        if (!in_array('Actions', $columns)) {
            $columns[] = 'Actions';
        }
    }

    public function getColumnAttributes($gridField, $record, $columnName)
    {
        return array('class' => 'col-buttons');
    }

    public function getColumnMetadata($gridField, $columnName)
    {
        if ($columnName == 'Actions') {
            return array('title' => '');
        }
    }

    public function getColumnsHandled($gridField)
    {
        return array('Actions');
    }

    public function getColumnContent($gridField, $record, $columnName)
    {
        if (!$record->canEdit()) return;

        $notification = $gridField->getList()->byID($record->ID);
        if($notification->Approved || $notification->isAlreadySent()) return;

        $title        = $notification->Approved ? "" : "Approved Notification";
        $icon         = $notification->Approved ? 'accept' : 'accept_disabled';

        $field = GridField_FormAction::create($gridField, 'approvesummitnotification' . $record->ID, false, "approvesummitnotification",
            array('RecordID' => $record->ID))
            ->setAttribute('title', $title)
            ->setAttribute('data-icon', $icon)
            ->addExtraClass('gridfield-button-approve-summit-notification')
            ->setDescription($title);

        return $field->Field();
    }

    public function getActions($gridField)
    {
        return array('approvesummitnotification');
    }

    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if ($actionName == 'approvesummitnotification') {

            $notification = $gridField->getList()->byID($arguments['RecordID']);
            $former_state = $notification->Approved;
            $msg          = 'Push notification Approved!';
            $code         = 200;

            try {
                if ($former_state || $notification->isAlreadySent()) {
                    return;
                }
                $notification->approve();
                $notification->write();

            } catch (Exception $ex) {
                throw new ValidationException($ex->getMessage(), 0);
            }

            Controller::curr()->getResponse()->setStatusCode($code, $msg);
        }
    }
}