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
class GridFieldWipeDevicesDataAction implements GridField_HTMLProvider, GridField_URLHandler
{
    protected $targetFragment;

    protected $gridField;

    private static $allowed_actions = array(
        'handleWipeDevicesDataAction',
        'handleGetAttendeesAction',
    );

    public function __construct() {
        $this->targetFragment = 'header';
    }

    public function getHTMLFragments($gridField)
    {
        $this->gridField = $gridField;
        Requirements::javascript('summit/javascript/GridFieldWipeDevicesDataAction.js');
        Requirements::css('summit/css/GridFieldWipeDevicesDataAction.css');

        $actions = array();
        $actions['wipe-all']  = 'Wipe all devices';
        $actions['wipe-user'] = 'Wipe user device';

        $field = new DropdownField(sprintf('%s[ActionID]', __CLASS__), '', $actions);
        $field->setEmptyString("-- select --");
        $field->addExtraClass('no-change-track');
        $field->addExtraClass('select-wipe-action');
        $data = new ArrayData(array(
            'Title'          => "Create Wipe Data Device Event",
            'Link'           => Controller::join_links($gridField->Link(), 'wipeDevicesDataAction', '{ActionID}'),
            'LinkAutocomple' => Controller::join_links($gridField->Link(), 'wipeDevicesGetAttendeesAction'),
            'ClassField' => $field,
        ));

        return array(
            $this->targetFragment => $data->renderWith(__CLASS__)
        );
    }

    public function getURLHandlers($gridField)
    {
        return array(
            'wipeDevicesDataAction/$ActionID!' => 'handleWipeDevicesDataAction',
            'wipeDevicesGetAttendeesAction'                => 'handleGetAttendeesAction',
        );
    }

    public function handleWipeDevicesDataAction($gridField, $request)
    {
        $action          = $request->param('ActionID');
        $attendee_id     = Convert::raw2sql($request->getVar('attendee_id'));
        $summit_id       = intval($request->param("ID"));
        $controller      = $gridField->getForm()->Controller();
        $this->gridField = $gridField;

        $entity_event    = SummitEntityEvent::create();
        $entity_event->EntityClassName = 'WipeData';
        $entity_event->SummitID = $summit_id;
        $entity_event->OwnerID  = Member::currentUserID();
        $entity_event->EntityID = $action === 'wipe-user' ? $attendee_id : 0;
        $entity_event->Type     = 'DELETE';
        $entity_event->write();
        $response = new SS_HTTPResponse();
        $response->setStatusCode(200);
        return $response;
    }

    public function handleGetAttendeesAction($gridField, $request)
    {
        $term      = Convert::raw2sql($request->getVar('term'));
        $summit_id = intval($request->param("ID"));
        $result = array();
        $sql = <<<SQL
SELECT A.ID,  CONCAT(M.FirstName,' ',M.Surname) AS FullName, M.Email  FROM SummitAttendee A INNER JOIN
Member M on M.ID = A.MemberID
WHERE A.SummitID = {$summit_id}
HAVING FullName LIKE '%{$term}%' ;
SQL;
        foreach(DB::query($sql) as $row)
        {

            array_push($result, array(
                'id' => $row['ID'],
                'label' => $row['FullName'].' ( '.$row['Email']. ' )',
            ));
        }

        $response = new SS_HTTPResponse(Convert::raw2json($result));
        $response->addHeader('Content-Type', 'text/json');
        $response->setStatusCode(200);
        return $response;
    }

}