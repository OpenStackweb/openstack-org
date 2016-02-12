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
class GridFieldDeleteAllSummitEntityEventsAction implements GridField_HTMLProvider, GridField_URLHandler
{
    protected $targetFragment;

    protected $gridField;

    private static $allowed_actions = array
    (
        'handleDeleteAllSummitEntityEventsAction',
    );

    public function getURLHandlers($gridField)
    {
        return array(
            'deleteAllSummitEntityEventsAction' => 'handleDeleteAllSummitEntityEventsAction',
        );
    }

    public function __construct() {
        $this->targetFragment = 'header';
    }

    public function getHTMLFragments($gridField)
    {
        $this->gridField = $gridField;
        Requirements::javascript('summit/javascript/GridFieldDeleteAllSummitEntityEventsAction.js');
        Requirements::css('summit/css/GridFieldDeleteAllSummitEntityEventsAction.css');


        $data = new ArrayData(array(
            'Title'          => "Delete All Summit Entity Events",
            'Link'           => Controller::join_links($gridField->Link(), 'deleteAllSummitEntityEventsAction'),
        ));

        return array(
            $this->targetFragment => $data->renderWith(__CLASS__)
        );
    }


    public function handleDeleteAllSummitEntityEventsAction($gridField, $request)
    {
        $summit_id       = intval($request->param("ID"));
        $controller      = $gridField->getForm()->Controller();
        $this->gridField = $gridField;


        $summit = Summit::get()->byID($summit_id);
        $status = 404;
        if(!is_null($summit))
        {
            $status = 200;
            DB::query("DELETE FROM SummitEntityEvent WHERE SummitID = {$summit_id} ;");
        }
        $response = new SS_HTTPResponse();
        $response->setStatusCode($status);
        return $response;
    }

}