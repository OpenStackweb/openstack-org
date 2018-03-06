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


final class SummitReferencialIntegrityMigration extends AbstractDBMigrationTask
{
    protected $title = "SummitReferencialIntegrityMigration";

    protected $description = "SummitReferencialIntegrityMigration";

    function doUp()
    {
        global $database;

        $sql = <<<SQL
        UPDATE SummitEvent SET RSVPTemplateID = NULL
WHERE NOT EXISTS (SELECT * FROM RSVPTemplate WHERE ID = SummitEvent.RSVPTemplateID);
ALTER TABLE SummitEvent ADD CONSTRAINT FK_CEA903D328C45601 FOREIGN KEY (RSVPTemplateID) REFERENCES RSVPTemplate (ID) ON DELETE SET NULL;     
UPDATE SummitEvent SET CategoryID = NULL
WHERE NOT EXISTS (SELECT * FROM PresentationCategory WHERE ID = SummitEvent.CategoryID);
ALTER TABLE SummitEvent ADD CONSTRAINT FK_CEA903D3E8042869 FOREIGN KEY (CategoryID) REFERENCES PresentationCategory (ID) ON DELETE SET NULL;     
UPDATE SummitEvent SET TypeID = NULL
WHERE NOT EXISTS (SELECT * FROM SummitEventType WHERE ID = SummitEvent.TypeID);
ALTER TABLE SummitEvent ADD CONSTRAINT FK_CEA903D3A736B16E FOREIGN KEY (TypeID) REFERENCES SummitEventType (ID) ON DELETE SET NULL;     
UPDATE SummitEvent SET LocationID = NULL
WHERE NOT EXISTS (SELECT * FROM SummitAbstractLocation WHERE ID = SummitEvent.LocationID);
ALTER TABLE SummitEvent ADD CONSTRAINT FK_CEA903D3E2E40B75 FOREIGN KEY (LocationID) REFERENCES SummitAbstractLocation (ID) ON DELETE SET NULL;     
DELETE  FROM SummitEvent_Sponsors 
WHERE NOT EXISTS (SELECT * FROM Company WHERE ID = SummitEvent_Sponsors.CompanyID);
ALTER TABLE SummitEvent_Sponsors ADD CONSTRAINT FK_1753E15F9D1F4548 FOREIGN KEY (CompanyID) REFERENCES Company (ID) ON DELETE CASCADE;     
DELETE  FROM Member_FavoriteSummitEvents 
WHERE NOT EXISTS (SELECT * FROM Member WHERE ID = Member_FavoriteSummitEvents.MemberID);
ALTER TABLE Member_FavoriteSummitEvents ADD CONSTRAINT FK_ADE58910522B9974 FOREIGN KEY (MemberID) REFERENCES Member (ID) ON DELETE CASCADE;     
DELETE FROM PresentationMaterial 
WHERE NOT EXISTS (SELECT * FROM Presentation WHERE ID = PresentationMaterial.PresentationID);
ALTER TABLE PresentationMaterial DROP FOREIGN KEY `FK_760FE6EC280A3317`;
ALTER TABLE PresentationMaterial ADD CONSTRAINT FK_760FE6EC280A3317 FOREIGN KEY (PresentationID) REFERENCES Presentation (ID) ON DELETE CASCADE;
UPDATE Presentation SET ModeratorID = NULL
WHERE NOT EXISTS (SELECT * FROM PresentationSpeaker WHERE ID = Presentation.ModeratorID);
ALTER TABLE Presentation ADD CONSTRAINT FK_8357336B1F04B5D2 FOREIGN KEY (ModeratorID) REFERENCES PresentationSpeaker (ID) ON DELETE SET NULL;
DELETE FROM Presentation_Speakers 
WHERE NOT EXISTS (SELECT * FROM Presentation WHERE ID = Presentation_Speakers.PresentationID);
ALTER TABLE Presentation_Speakers ADD CONSTRAINT FK_5D54C680280A3317 FOREIGN KEY (PresentationID) REFERENCES Presentation (ID) ON DELETE CASCADE;     
DELETE FROM Presentation_Speakers 
WHERE NOT EXISTS (SELECT * FROM PresentationSpeaker WHERE ID = Presentation_Speakers.PresentationSpeakerID);
ALTER TABLE Presentation_Speakers ADD CONSTRAINT FK_5D54C68055E7310E FOREIGN KEY (PresentationSpeakerID) REFERENCES PresentationSpeaker (ID) ON DELETE CASCADE;     
DELETE FROM RSVPAnswer 
WHERE NOT EXISTS (SELECT * FROM RSVP WHERE ID = RSVPAnswer.RSVPID);
ALTER TABLE RSVPAnswer ADD CONSTRAINT FK_C99BBEF65DE7B24F FOREIGN KEY (RSVPID) REFERENCES RSVP (ID) ON DELETE CASCADE;
ALTER TABLE SummitEvent_Sponsors ADD CONSTRAINT FK_1753E15F22CF6AF5 FOREIGN KEY (SummitEventID) REFERENCES SummitEvent (ID) ON DELETE CASCADE;     
ALTER TABLE ScheduleCalendarSyncInfo ADD CONSTRAINT FK_11BDB9E9148DE471 FOREIGN KEY (OwnerID) REFERENCES Member (ID) ON DELETE CASCADE;   
ALTER TABLE RSVP ADD CONSTRAINT FK_A93023FAE53DEA85 FOREIGN KEY (SubmittedByID) REFERENCES Member (ID) ON DELETE CASCADE;     
ALTER TABLE RSVP ADD CONSTRAINT FK_A93023FADFFDA238 FOREIGN KEY (EventID) REFERENCES SummitEvent (ID) ON DELETE CASCADE;     
ALTER TABLE Member_FavoriteSummitEvents ADD CONSTRAINT FK_ADE5891022CF6AF5 FOREIGN KEY (SummitEventID) REFERENCES SummitEvent (ID) ON DELETE CASCADE;     
ALTER TABLE Member_Schedule ADD CONSTRAINT FK_DC56EED9522B9974 FOREIGN KEY (MemberID) REFERENCES Member (ID) ON DELETE CASCADE;     
ALTER TABLE Member_Schedule ADD CONSTRAINT FK_DC56EED922CF6AF5 FOREIGN KEY (SummitEventID) REFERENCES SummitEvent (ID) ON DELETE CASCADE;     
SQL;

        $statements = explode(';', $sql);
        foreach($statements as $statement){
            $statement = trim($statement);
            if(empty($statement)) continue;
            DB::query($statement);
        }

    }
}