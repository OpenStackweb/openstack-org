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
class GridFieldBulkActionAssignSummitTypeSummitEvents extends GridFieldBulkAction
{

    protected function getEntities()
    {
        $summit_id = intval($_REQUEST['SummitID']);
        $res = SummitType::get()->filter('SummitID', $summit_id)->map('ID', 'Title');
        $additional_actions = array();
        foreach ($res->toArray() as $k => $v) {
            $additional_actions[$k . '_ALL'] = $v . ' ( COMPLETE RECORDSET )';
        }
        foreach ($additional_actions as $k => $v) {
            $res->push($k, $v);
        }

        return $res;
    }

    protected function processRecordIds(array $ids, $entity_id, $gridField, $request)
    {
        $summit_id = intval($request->param('ID'));
        if ((is_null($ids) || count($ids) === 0) && strstr($entity_id, '_ALL') !== false) {
            $entity_id = explode('_', $entity_id);
            $entity_id = intval($entity_id[0]);


            $query = <<<SQL
INSERT INTO SummitEvent_AllowedSummitTypes
(
`SummitEventID`,
`SummitTypeID`)
SELECT ID, {$entity_id} FROM SummitEvent WHERE SummitID = {$summit_id} AND ClassName = '{$gridField->getModelClass()}'
AND NOT EXISTS (SELECT 1 FROM SummitEvent_AllowedSummitTypes
WHERE
SummitEvent_AllowedSummitTypes.SummitEventID = SummitEvent.ID AND SummitEvent_AllowedSummitTypes.SummitTypeID = {$entity_id});
SQL;

            DB::query($query);
        }
        else {
            foreach ($ids as $id) {
                $event = SummitEvent::get()->byID($id);
                if (is_null($event)) {
                    continue;
                }
                $event->AllowedSummitTypes()->add($entity_id);
            }
        }
    }
}