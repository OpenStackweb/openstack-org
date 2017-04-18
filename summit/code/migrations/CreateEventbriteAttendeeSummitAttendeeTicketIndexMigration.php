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
class CreateEventbriteAttendeeSummitAttendeeTicketIndexMigration extends AbstractDBMigrationTask
{
    protected $title = "CreateEventbriteAttendeeSummitAttendeeTicketIndexMigration";

    protected $description = "CreateEventbriteAttendeeSummitAttendeeTicketIndexMigration";

    function doUp()
    {
        global $database;

        if (!DBSchema::existsIndex($database, 'EventbriteAttendee', 'idx_EventbriteAttendee_ExternalAttendeeId')) {

            DB::query("CREATE INDEX idx_EventbriteAttendee_ExternalAttendeeId
ON EventbriteAttendee (ExternalAttendeeId);");
        }

        if (!DBSchema::existsIndex($database, 'EventbriteAttendee', 'idx_EventbriteAttendee_Email_FirstName_LastName')) {

            DB::query("CREATE INDEX idx_EventbriteAttendee_Email_FirstName_LastName
ON EventbriteAttendee (Email, FirstName, LastName);");
        }

        if (!DBSchema::existsIndex($database, 'EventbriteAttendee', 'idx_EventbriteAttendee_ExternalTicketClassId')) {

            DB::query("CREATE INDEX idx_EventbriteAttendee_ExternalTicketClassId
ON EventbriteAttendee (ExternalTicketClassId);");
        }

        if (!DBSchema::existsIndex($database, 'SummitAttendeeTicket', 'idx_SummitAttendeeTicket_ExternalAttendeeId')) {

            DB::query("CREATE INDEX idx_SummitAttendeeTicket_ExternalAttendeeId
ON SummitAttendeeTicket (ExternalAttendeeId);");
        }

        if (!DBSchema::existsIndex($database, 'SummitAttendeeTicket', 'idx_SummitAttendeeTicket_ExternalOrderId')) {

            DB::query("CREATE INDEX idx_SummitAttendeeTicket_ExternalOrderId
ON SummitAttendeeTicket (ExternalOrderId);");
        }
    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}