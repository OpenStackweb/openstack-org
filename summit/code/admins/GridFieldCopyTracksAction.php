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
class GridFieldCopyTracksAction implements GridField_HTMLProvider, GridField_URLHandler
{
    protected $targetFragment;

    protected $gridField;

    protected $summit_id;

    private static $allowed_actions = array
    (
        'handleCopyTracksAction',
    );

    public function __construct($summit_id) {
        $this->targetFragment = 'header';
        $this->summit_id = $summit_id;
    }

    public function getHTMLFragments($gridField)
    {
        $this->gridField = $gridField;
        Requirements::javascript('summit/javascript/GridFieldCopyTracksAction.js');
        Requirements::css('summit/css/GridFieldCopyTracksAction.css');

        $summits = Summit::get()
            ->filter(['ID:not' => $this->summit_id])
            ->sort('SummitBeginDate','DESC')
            ->limit(3)
            ->map('ID','Title');

        $field = new DropdownField(sprintf('%s[SummitID]', __CLASS__), '', $summits);
        $field->setEmptyString("-- select a summit --");
        $field->addExtraClass('no-change-track');
        $field->addExtraClass('select-summit-source');

        $data = new ArrayData(array(
            'Title'          => "Copy Tracks from Summit",
            'Link'           => Controller::join_links($gridField->Link(), 'copyTracksAction', '{SummitID}'),
            'ClassField' => $field,
        ));

        return array(
            $this->targetFragment => $data->renderWith(__CLASS__)
        );
    }

    public function getURLHandlers($gridField)
    {
        return array(
            'copyTracksAction/$SummitID!' => 'handleCopyTracksAction',
        );
    }

    public function handleCopyTracksAction($gridField, $request)
    {
        $source_summit_id = intval($request->param('SummitID'));
        $summit_id        = intval($request->param("ID"));
        $this->gridField  = $gridField;

        $source_summit = Summit::get()->byID($source_summit_id);
        $summit = Summit::get()->byID($summit_id);

        foreach($source_summit->CategoryGroups() as $track_group) {
            if ( !$new_track_group = $summit->CategoryGroups()->find('Name', $track_group->Name)) {
                $new_track_group = $track_group->duplicate(false);
                $new_track_group->SummitID = $summit_id;
                $new_track_group->write();
            }

            foreach ($track_group->Categories() as $cat) {
                if ( !$new_cat = $summit->Categories()->find('Title', $cat->Title)) {
                    $new_cat = $cat->duplicate(false);
                    $new_cat->SummitID = $summit_id;
                    $new_cat->write();
                }

                foreach ($cat->ExtraQuestions() as $extraq) {
                    if (strpos($extraq->Name, '_copy_')) {
                        $new_name = substr($extraq->Name, 0, -1).$summit_id;
                    } else {
                        $new_name = $extraq->Name.'_copy_'.$summit_id;
                    }

                    if ( !$new_extraq = TrackQuestionTemplate::get()->filter('Name', $new_name)->first()) {
                        $new_extraq = $extraq->duplicate(false);
                        $new_extraq->Name = $new_name;
                        $new_extraq->write();
                    }

                    $new_cat->ExtraQuestions()->add($new_extraq);
                }

                $new_track_group->Categories()->add($new_cat);
            }
        }

        $response = new SS_HTTPResponse();
        $response->setStatusCode(200);
        return $response;
    }

}