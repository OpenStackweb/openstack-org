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
final class UpdateAttendeesTicketsMigration extends AbstractDBMigrationTask
{
    protected $title = "Update Attendees Tickets";

    protected $description = "Update Attendees Tickets from one2one to many2many";

    function doUp()
    {
        global $database;

        if (DBSchema::existsColumn($database, 'SummitAttendee', 'TicketTypeID')) {

            $attendees =  DB::query("SELECT * from SummitAttendee;");

            foreach($attendees as $attendee)
            {
                $attendee = new SummitAttendee($attendee);
                $ticket = SummitTicketType::get()->byID(intval($attendee->TicketTypeID));
                $attendee->TicketTypes()->add($ticket);
            }
            DB::query("ALTER TABLE SummitAttendee DROP COLUMN TicketTypeID;");
            DB::query("ALTER TABLE SummitAttendee DROP COLUMN ExternalTicketClassID;");

        }
    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}