<?php

/**
 * Copyright 2017 OpenStack Foundation
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
final class PermissionModelMigration extends AbstractDBMigrationTask
{
    protected $title = "PermissionModelMigration";

    protected $description = "PermissionModelMigration";

    function doUp()
    {
        global $database;
        // update RSVP owner from attendee to members
        $query1 = <<<SQL
UPDATE RSVP 
INNER JOIN SummitAttendee ON SummitAttendee.ID = RSVP.SubmittedByID
INNER JOIN Member ON Member.ID = SummitAttendee.MemberID
SET RSVP.SubmittedByID = SummitAttendee.MemberID
SQL;

        DB::query($query1);

        // update schedule owner from attendee to members

        $query2 = <<<SQL
INSERT INTO Member_Schedule
(MemberID, SummitEventID)
SELECT SummitAttendee.MemberID, SummitEventId from SummitAttendee_Schedule
INNER JOIN SummitAttendee ON SummitAttendee.ID = SummitAttendee_Schedule.SummitAttendeeID
INNER JOIN Member ON Member.ID = SummitAttendee.MemberID;
SQL;

        DB::query($query2);

       DBSchema::dropTable($database, "SummitAttendee_Schedule");
        // update email template

        $query3 = <<<SQL
        
UPDATE PermamailTemplate set Content =
'<p>Thank you for your RSVP to <strong>{\$Event.Title}</strong> on {\$Event.getDateNice} . For your convenience, we have added this to My Schedule within the Summit Management tool.</p>
<p>Be sure to synch it to your calendar by going <a href="{\$ScheduleURL}">here</a>.</p>

Please present a printed copy of this email at the entrance where the event is beign held.<br/><br/>

****************************************************************************************** <br/>
<p>
Attendee: {\$Member.FirstName} {\$Member.Surname} <br/>
Event: {\$Event.Title} <br/>
Confirmation #: {\$ConfirmationNbr} <br/>
</p>
****************************************************************************************** <br/>

<p>Cheers,</p><p>OpenStack Summit Team</p>'
WHERE Identifier = 'summit-attendee-rsvp';
SQL;

        DB::query($query3);

    }

    function doDown()
    {

    }
}