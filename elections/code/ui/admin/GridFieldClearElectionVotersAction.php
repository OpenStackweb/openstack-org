<?php
/**
 * Copyright 2018 Open Infrastructure Foundation
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

final class GridFieldClearElectionVotersAction
    implements GridField_HTMLProvider,
    GridField_URLHandler,
    GridField_ActionProvider
{

    protected $targetFragment;

    private static $allowed_actions = array(
        'handleClearElectionVoters'
    );

    public function __construct($targetFragment = 'before') {
        $this->targetFragment = $targetFragment;
    }

    //Generate the HTML fragment for the GridField
    public function getHTMLFragments($gridField) {
        $button = new GridField_FormAction(
            $gridField,
            'clearElectionVoters',
            'Clear Election Voters',
            'clearElectionVoters',
            null
        );
        $button->setAttribute('data-icon', 'cross-circle');
        $button->addExtraClass("clear-election-voters");
        Requirements::javascript('elections/javascript/GridFieldClearElectionVotersAction.js');
        return array(
            $this->targetFragment =>  $button->Field() ,
        );
    }
    /**
     * Return URLs to be handled by this grid field, in an array the same form
     * as $url_handlers.
     * Handler methods will be called on the component, rather than the
     * {@link GridField}.
     */
    public function getURLHandlers($gridField)
    {
        return array(
            'clearElectionVoters' => 'handleClearElectionVoters'
        );
    }

    public function handleClearElectionVoters($grid, $request, $data = null) {

        $election_id = intval($request->param('ID'));
        if($election_id > 0 && $election = Election::get()->byID($election_id))
        {
            $election->clearVoters()->write();
        }
    }

    public function getActions($gridField) {
        return array('clearElectionVoters');
    }

    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if($actionName == 'clearelectionvoters') {
            return $this->handleClearElectionVoters($gridField, Controller::curr()->getRequest(), $data);
        }
    }
}

