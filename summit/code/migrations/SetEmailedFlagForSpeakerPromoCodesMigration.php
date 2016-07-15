<?php

/**
 * Copyright 2016 OpenStack Foundation
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
final class SetEmailedFlagForSpeakerPromoCodesMigration extends AbstractDBMigrationTask
{
    protected $title = "SetEmailedFlagForSpeakerPromoCodesMigration";

    protected $description = "SetEmailedFlagForSpeakerPromoCodesMigration";

    function doUp()
    {
        global $database;

        $res = DB::query("
            UPDATE SummitRegistrationPromoCode AS PC SET PC.EmailSent = 1
            WHERE EXISTS(SELECT * FROM SpeakerSummitRegistrationPromoCode AS S WHERE S.ID = PC.ID AND S.SpeakerID IS NOT NULL)
        ");
    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}