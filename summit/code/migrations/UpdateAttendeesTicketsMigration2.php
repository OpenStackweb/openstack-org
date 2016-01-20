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
final class UpdateAttendeesTicketsMigration2 extends AbstractDBMigrationTask
{
    protected $title = "UpdateAttendeesTicketsMigration2";

    protected $description = "Update Attendees Tickets from one2one to many2many";

    function doUp()
    {
        global $database;

        if (DBSchema::existsColumn($database, 'SummitAttendee', 'TicketTypeID')) {

            DB::query("DELETE FROM SummitAttendee;");
            DB::query("UPDATE EventbriteEvent SET Processed = 0;");
            DB::query("ALTER TABLE SummitAttendee DROP COLUMN TicketTypeID;");
            DB::query("ALTER TABLE SummitAttendee DROP COLUMN ExternalTicketClassID;");
            DB::query("ALTER TABLE SummitAttendee DROP COLUMN ExternalOrderId;");
            DB::query("ALTER TABLE SummitAttendee DROP COLUMN TicketBoughtDate;");
            DB::query("ALTER TABLE SummitAttendee DROP COLUMN ExternalId;");
        }
    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}