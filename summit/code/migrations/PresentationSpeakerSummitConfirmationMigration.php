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
class PresentationSpeakerSummitConfirmationMigration extends AbstractDBMigrationTask
{
    protected $title = "PresentationSpeakerSummitConfirmationMigration";

    protected $description = "PresentationSpeakerSummitConfirmationMigration";

    function doUp()
    {
        global $database;

        if (DBSchema::existsColumn($database, 'PresentationSpeaker', 'OnSitePhoneNumber')) {

            $speakers_info_query = <<<SQL
SELECT ID, OnSitePhoneNumber, ConfirmedDate, RegisteredForSummit
FROM PresentationSpeaker where OnSitePhoneNumber IS NOT NULL;
SQL;

            $res = DB::query($speakers_info_query);
            foreach($res as $row)
            {
                $speaker_id     = intval($row['ID']);
                $on_site_phone  = intval($row['OnSitePhoneNumber']);
                $confirmed_date = $row['ConfirmedDate'];
                $registered     = (bool)$row['RegisteredForSummit'];
                $speaker        = PresentationSpeaker::get()->byID($speaker_id);
                if(is_null($speaker)) continue;

                if(PresentationSpeakerSummitAssistanceConfirmationRequest::get()->filter(
                        array('SummitID' => 5, 'SpeakerID' => $speaker_id)
                    )->count() > 0) continue;
                $assistance                       = PresentationSpeakerSummitAssistanceConfirmationRequest::create();
                $assistance->SpeakerID            = $speaker_id;
                $assistance->SummitID             = 5;
                $assistance->OnSitePhoneNumber    = $on_site_phone;
                $assistance->ConfirmationDate     = $confirmed_date;
                $assistance->RegisteredForSummit  = $registered;
                $assistance->IsConfirmed          = true;
                $assistance->write();

            }
            DBSchema::dropColumn($database, 'PresentationSpeaker', 'OnSitePhoneNumber');
            DBSchema::dropColumn($database, 'PresentationSpeaker', 'ConfirmedDate');
            DBSchema::dropColumn($database, 'PresentationSpeaker', 'RegisteredForSummit');
        }


    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}