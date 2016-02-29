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
class UpdatePresentationSpeakerMigration extends AbstractDBMigrationTask
{
    protected $title = "UpdatePresentationSpeakerMigration";

    protected $description = "UpdatePresentationSpeakerMigration";

    function doUp()
    {
        global $database;

        if (DBSchema::existsColumn($database, 'PresentationSpeaker', 'SummitID')) {

            DBSchema::dropColumn($database, 'PresentationSpeaker', 'SummitID');
        }

        if (DBSchema::existsColumn($database, 'PresentationSpeaker', 'AnnouncementEmailTypeSent')) {

            DBSchema::dropColumn($database, 'PresentationSpeaker', 'AnnouncementEmailTypeSent');
        }

        if (DBSchema::existsColumn($database, 'PresentationSpeaker', 'AnnouncementEmailSentDate')) {

            DBSchema::dropColumn($database, 'PresentationSpeaker', 'AnnouncementEmailSentDate');
        }

        if (DBSchema::existsColumn($database, 'PresentationSpeaker', 'SummitRegistrationPromoCodeID')) {

            DBSchema::dropColumn($database, 'PresentationSpeaker', 'SummitRegistrationPromoCodeID');
        }

        if (DBSchema::existsColumn($database, 'PresentationSpeaker', 'TwitterHandle')) {

            DB::query('UPDATE PresentationSpeaker SET TwitterName = TwitterHandle;');
            DBSchema::dropColumn($database, 'PresentationSpeaker', 'TwitterHandle');
        }
    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}