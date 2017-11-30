<?php
/**
 * Copyright 2017 OpenStack Foundation
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

class GridFieldRenewFoundationMembershipAction
    implements GridField_ColumnProvider, GridField_ActionProvider
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
        if (!$record->canEdit()) {
            return;
        }
        $revocation_notification  = $gridField->getList()->byID($record->ID);
        $allowed                  = $revocation_notification->isValid();

        $action_txt = '';
        switch($revocation_notification->Action){
            case 'Renew': $action_txt   = 'Renewed';break;
            case 'Revoked': $action_txt = 'Revoked';break;
            case 'Resign': $action_txt  = 'Resigned';break;
        }

        $title = $allowed ? "Renew Foundation Membership" : sprintf("Membership %s",$action_txt );
        $icon  = $allowed ? 'chain--exclamation' : 'chain-unchain';
        $field = GridField_FormAction::create($gridField, 'renewfoundationmembership' . $record->ID, false,
            "renewfoundationmembership",
            array('RecordID' => $record->ID))
            ->setAttribute('title', $title)
            ->setAttribute('data-icon', $icon)
            ->setDescription($title);
        $field->setDisabled(!$allowed);

        return $field->Field();
    }

    public function getActions($gridField)
    {
        return array('renewfoundationmembership');
    }

    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if ($actionName == 'renewfoundationmembership') {
            $revocation_notification = $gridField->getList()->byID($arguments['RecordID']);
            $allowed        = $revocation_notification->isValid();
            $msg            = 'This user is already a renewed Foundation Membership!';
            if ($allowed) {
                $revocation_notification->renew();
                $revocation_notification->write();
                $msg       = 'User is now a Foundation Member';
            }
            Controller::curr()->getResponse()->setStatusCode(200, $msg);
        }
    }
}