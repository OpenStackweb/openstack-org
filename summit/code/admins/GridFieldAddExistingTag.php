<?php
/**
 * Copyright 2019 OpenStack Foundation
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
 * Class GridFieldAddExistingTag
 */
final class GridFieldAddExistingTag extends GridFieldAddExistingAutocompleter {


    /**
     * Manipulate the state to add a new relation
     *
     * @param GridField $gridField
     * @param string $actionName Action identifier, see {@link getActions()}.
     * @param array $arguments Arguments relevant for this
     * @param array $data All form data
     */
    public function handleAction(GridField $gridField, $actionName, $arguments, $data) {
        parent::handleAction($gridField, $actionName, $arguments, $data);

        if(isset($data['relationID']) && $data['relationID'] && isset($data['SummitID']) && $data['SummitID']) {
            Summit::seedTagOnAllTracksAllowedTags(
                Summit::get()->byID($data['SummitID']),
                Tag::get()->byID($data['relationID'])
            );
        }
    }

}

