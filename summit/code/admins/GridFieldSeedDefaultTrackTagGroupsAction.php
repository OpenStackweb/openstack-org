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

/**
 * Class GridFieldSeedDefaultTrackTagGroupsAction
 */
final class GridFieldSeedDefaultTrackTagGroupsAction
    implements GridField_HTMLProvider, GridField_URLHandler, GridField_ActionProvider {

    protected $targetFragment;

    private static $allowed_actions = [
        'handleGridFieldSeedDefaultTrackTagGroupsAction'
    ];

    public function __construct($targetFragment = 'before') {
        $this->targetFragment = $targetFragment;
    }

    //Generate the HTML fragment for the GridField
    public function getHTMLFragments($gridField) {
        $button = new GridField_FormAction(
            $gridField,
            'seedDefaultTrackTagGroupsAction',
            'Seed Default Track Tag Groups',
            'seedDefaultTrackTagGroupsAction',
            null
        );
        $button->setAttribute('data-icon', 'add');
        return [
            $this->targetFragment =>  $button->Field() ,
        ];
    }
    /**
     * Return URLs to be handled by this grid field, in an array the same form
     * as $url_handlers.
     * Handler methods will be called on the component, rather than the
     * {@link GridField}.
     */
    public function getURLHandlers($gridField)
    {
        return [
            'seedDefaultTrackTagGroupsAction' => 'handleSeedDefaultTrackTagGroupsAction'
        ];
    }

    public function handleSeedDefaultTrackTagGroupsAction($grid, $request, $data = null) {

        $summit_id = intval($request->param('ID'));
        if($summit_id > 0 && $summit = Summit::get()->byID($summit_id))
        {
            Summit::seedDefaultTrackTagGroups($summit);
        }
    }

    public function getActions($gridField) {
        return ['seedDefaultTrackTagGroupsAction'];
    }

    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if($actionName == 'seeddefaulttracktaggroupsaction') {
            return $this->handleSeedDefaultTrackTagGroupsAction($gridField,Controller::curr()->getRequest(), $data);
        }
    }
}
