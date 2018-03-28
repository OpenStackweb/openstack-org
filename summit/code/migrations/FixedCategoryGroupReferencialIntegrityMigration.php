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

final class FixedCategoryGroupReferencialIntegrityMigration
    extends AbstractDBMigrationTask
{
    protected $title = "UpdateRSVPReferencialIntegrityMigration";

    protected $description = "UpdateRSVPReferencialIntegrityMigration";

    function doUp()
    {
        $sql = <<<SQL
ALTER TABLE PrivatePresentationCategoryGroup ADD CONSTRAINT FK_CB5CE87711D3633A FOREIGN KEY (ID) REFERENCES PresentationCategoryGroup (ID) ON DELETE CASCADE;     
DELETE FROM PrivatePresentationCategoryGroup_AllowedGroups WHERE 
NOT EXISTS ( SELECT ID FROM PrivatePresentationCategoryGroup WHERE ID = PrivatePresentationCategoryGroupID);
ALTER TABLE PrivatePresentationCategoryGroup_AllowedGroups ADD CONSTRAINT FK_62B748C1DF7C2D1D FOREIGN KEY (PrivatePresentationCategoryGroupID) REFERENCES PrivatePresentationCategoryGroup (ID) ON DELETE CASCADE;     
ALTER TABLE PrivatePresentationCategoryGroup_AllowedGroups ADD CONSTRAINT FK_62B748C1195291E4 FOREIGN KEY (GroupID) REFERENCES `Group` (ID) ON DELETE CASCADE;
SQL;

        $statements = explode(';', $sql);
        foreach($statements as $statement){
            $statement = trim($statement);
            if(empty($statement)) continue;
            DB::query($statement);
        }

    }
}