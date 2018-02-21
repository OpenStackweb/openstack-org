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
 * Class AddDefaultEventTypesPresentationTypesMigration
 */
final class AddDefaultEventTypesPresentationTypesMigration extends AbstractDBMigrationTask
{
    protected $title = "AddDefaultEventTypesPresentationTypesMigration";

    protected $description = "AddDefaultEventTypesPresentationTypesMigration";

    function doUp()
    {
        $presentation = DefaultPresentationType::get()->filter(['Type' => IPresentationType::Presentation])->first();
        if (is_null($presentation)) {
            $presentation = new DefaultPresentationType();
        }

        $presentation->Type                   = IPresentationType::Presentation;
        $presentation->MinSpeakers            = 1;
        $presentation->MaxSpeakers            = 3;
        $presentation->MinModerators          = 0;
        $presentation->MaxModerators          = 0;
        $presentation->UseSpeakers            = true;
        $presentation->ShouldBeAvailableOnCFP = true;
        $presentation->AreSpeakersMandatory   = false;
        $presentation->UseModerator           = false;
        $presentation->IsModeratorMandatory   = false;
        $presentation->write();

        $key_note = DefaultPresentationType::get()->filter(['Type' => IPresentationType::Keynotes])->first();
        if (is_null($key_note)) {
            $key_note = new DefaultPresentationType();
        }

        $key_note->Type                   = IPresentationType::Keynotes;
        $key_note->MinSpeakers            = 1;
        $key_note->MaxSpeakers            = 3;
        $key_note->MinModerators          = 0;
        $key_note->MaxModerators          = 1;
        $key_note->ShouldBeAvailableOnCFP = false;
        $key_note->UseSpeakers            = true;
        $key_note->AreSpeakersMandatory   = false;
        $key_note->UseModerator           = true;
        $key_note->IsModeratorMandatory   = false;
        $key_note->write();

        $panel = DefaultPresentationType::get()->filter(['Type' => IPresentationType::Panel])->first();
        if (is_null($panel)) {
            $panel = new DefaultPresentationType();
        }

        $panel->Type                   = IPresentationType::Panel;
        $panel->MinSpeakers            = 1;
        $panel->MaxSpeakers            = 3;
        $panel->MinModerators          = 0;
        $panel->MaxModerators          = 1;
        $panel->ShouldBeAvailableOnCFP = true;
        $panel->UseSpeakers            = true;
        $panel->AreSpeakersMandatory   = false;
        $panel->UseModerator           = true;
        $panel->IsModeratorMandatory   = false;
        $panel->write();

        $lighting_talks = DefaultPresentationType::get()->filter(['Type' => IPresentationType::LightingTalks])->first();
        if (is_null($lighting_talks)) {
            $lighting_talks = new DefaultPresentationType();
        }

        $lighting_talks->Type                   = IPresentationType::LightingTalks;
        $lighting_talks->MinSpeakers            = 1;
        $lighting_talks->MaxSpeakers            = 3;
        $lighting_talks->MinModerators          = 0;
        $lighting_talks->MaxModerators          = 0;
        $lighting_talks->UseSpeakers            = true;
        $lighting_talks->ShouldBeAvailableOnCFP = true;
        $lighting_talks->AreSpeakersMandatory   = false;
        $lighting_talks->UseModerator           = false;
        $lighting_talks->IsModeratorMandatory   = false;
        $lighting_talks->write();

        $hand_on_labs = DefaultSummitEventType::get()->filter(['Type' => ISummitEventType::HandonLabs])->first();
        if (is_null($hand_on_labs)) {
            $hand_on_labs = new DefaultSummitEventType();

        }

        $hand_on_labs->Type = ISummitEventType::HandonLabs;
        $hand_on_labs->write();

        $lunch = DefaultSummitEventType::get()->filter(['Type' => ISummitEventType::Lunch])->first();
        if (is_null($lunch)) {
            $lunch = new DefaultSummitEventType();
        }

        $lunch->AllowsAttachment = true;
        $lunch->Type             = ISummitEventType::Lunch;
        $lunch->write();

        $breaks = DefaultSummitEventType::get()->filter(['Type' => ISummitEventType::Breaks])->first();
        if (is_null($breaks)) {
            $breaks = new DefaultSummitEventType();
        }

        $breaks->Type = ISummitEventType::Breaks;
        $breaks->write();

        $evening_events = DefaultSummitEventType::get()->filter(['Type' => ISummitEventType::EveningEvents])->first();
        if (is_null($evening_events)) {
            $evening_events = new DefaultSummitEventType();
        }

        $evening_events->Type     = ISummitEventType::EveningEvents;
        $evening_events->write();

        $groups_events = DefaultSummitEventType::get()->filter(['Type' => ISummitEventType::GroupsEvents])->first();
        if (is_null($groups_events)) {
            $groups_events = new DefaultSummitEventType();
        }

        $groups_events->Type      = ISummitEventType::GroupsEvents;
        $groups_events->IsPrivate = true;
        $groups_events->write();

        DB::query("UPDATE SummitEventType set IsPrivate = 1 WHERE Type = 'Groups Events'");
    }

}