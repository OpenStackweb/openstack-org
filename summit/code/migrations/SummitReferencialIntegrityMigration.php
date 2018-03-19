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
DELETE FROM Presentation_Speakers 
WHERE NOT EXISTS (SELECT * FROM Presentation WHERE ID = Presentation_Speakers.PresentationID);
ALTER TABLE Presentation_Speakers ADD CONSTRAINT FK_5D54C680280A3317 FOREIGN KEY (PresentationID) REFERENCES Presentation (ID) ON DELETE CASCADE;     
DELETE FROM Presentation_Speakers 
WHERE NOT EXISTS (SELECT * FROM PresentationSpeaker WHERE ID = Presentation_Speakers.PresentationSpeakerID);
ALTER TABLE Presentation_Speakers ADD CONSTRAINT FK_5D54C68055E7310E FOREIGN KEY (PresentationSpeakerID) REFERENCES PresentationSpeaker (ID) ON DELETE CASCADE;
DELETE  FROM Member_FavoriteSummitEvents 
WHERE NOT EXISTS (SELECT * FROM Member WHERE ID = Member_FavoriteSummitEvents.MemberID);
ALTER TABLE Member_FavoriteSummitEvents ADD CONSTRAINT FK_ADE58910522B9974 FOREIGN KEY (MemberID) REFERENCES Member (ID) ON DELETE CASCADE;          
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