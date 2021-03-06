<?php
/**
 * Copyright 2018 OpenStack Foundation
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

final class GridFieldSeedAllowedTagOnAllSummitTracksColumnAction
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
        return ['class' => 'col-buttons'];
    }

    public function getColumnMetadata($gridField, $columnName)
    {
        if ($columnName == 'Actions') {
            return ['title' => ''];
        }
    }

    public function getColumnsHandled($gridField)
    {
        return array('Actions');
    }

    public function getColumnContent($gridField, $record, $columnName)
    {
        if (!$record->canEdit()) return;

        $tag = $gridField->getList()->byID($record->ID);

        $title = 'Seed Tag on All Tracks Allowed Tags';
        $icon  = 'add' ;

        $field = GridField_FormAction::create($gridField, 'seedallowedtagonallsummittracks' . $record->ID, false, "seedallowedtagonallsummittracks",
            ['RecordID' => $record->ID])
            ->setAttribute('title', $title)
            ->setAttribute('data-icon', $icon)
            ->setDescription($title);

        return $field->Field();
    }

    public function getActions($gridField)
    {
        return ['seedallowedtagonallsummittracks'];
    }

    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if ($actionName == 'seedallowedtagonallsummittracks') {

            $tag       = $gridField->getList()->byID($arguments['RecordID']);
            $summit_id = $_REQUEST['SummitID'];

            Summit::seedTagOnAllTracksAllowedTags(
                Summit::get()->byID($summit_id),
                $tag
            );
            $code = 200;
            $msg  = 'tag added sucessfull from all summit tracks';

            try {

            } catch (Exception $ex) {
                throw new ValidationException($ex->getMessage(), 0);
            }

            Controller::curr()->getResponse()->setStatusCode($code, $msg);
        }
    }
}