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

/**
 * Class UpdateRSVPReferencialIntegrityMigration
 */
final class UpdateRSVPReferencialIntegrityMigration extends AbstractDBMigrationTask
{

    protected $title = "UpdateRSVPReferencialIntegrityMigration";

    protected $description = "UpdateRSVPReferencialIntegrityMigration";

    function doUp()
    {
        $sql = <<<SQL
       ALTER TABLE RSVPQuestionTemplate ADD CONSTRAINT FK_D27AA25028C45601 FOREIGN KEY (RSVPTemplateID) REFERENCES RSVPTemplate (ID) ON DELETE CASCADE;     
ALTER TABLE RSVPSingleValueTemplateQuestion ADD CONSTRAINT FK_20DDFA511D3633A FOREIGN KEY (ID) REFERENCES RSVPQuestionTemplate (ID) ON DELETE CASCADE;     
ALTER TABLE RSVPTextBoxQuestionTemplate ADD CONSTRAINT FK_B23A250311D3633A FOREIGN KEY (ID) REFERENCES RSVPQuestionTemplate (ID) ON DELETE CASCADE;     
ALTER TABLE RSVPMemberFirstNameQuestionTemplate ADD CONSTRAINT FK_A4653D7111D3633A FOREIGN KEY (ID) REFERENCES RSVPQuestionTemplate (ID) ON DELETE CASCADE;     
ALTER TABLE RSVPLiteralContentQuestionTemplate ADD CONSTRAINT FK_750444E611D3633A FOREIGN KEY (ID) REFERENCES RSVPQuestionTemplate (ID) ON DELETE CASCADE;     
ALTER TABLE RSVPTextAreaQuestionTemplate ADD CONSTRAINT FK_8865FD4E11D3633A FOREIGN KEY (ID) REFERENCES RSVPQuestionTemplate (ID) ON DELETE CASCADE;     
ALTER TABLE RSVPQuestionTemplate_DependsOn ADD CONSTRAINT FK_6F21D45793B02ED0 FOREIGN KEY (RSVPQuestionTemplateID) REFERENCES RSVPQuestionTemplate (ID) ON DELETE CASCADE;     
ALTER TABLE RSVPQuestionTemplate_DependsOn ADD CONSTRAINT FK_6F21D457F62E7213 FOREIGN KEY (ChildID) REFERENCES RSVPQuestionTemplate (ID) ON DELETE CASCADE;     
ALTER TABLE RSVPQuestionValueTemplate ADD CONSTRAINT FK_4C0EE7E0148DE471 FOREIGN KEY (OwnerID) REFERENCES RSVPMultiValueQuestionTemplate (ID);     
ALTER TABLE RSVPMultiValueQuestionTemplate ADD CONSTRAINT FK_DFF16B5011D3633A FOREIGN KEY (ID) REFERENCES RSVPQuestionTemplate (ID) ON DELETE CASCADE;     
ALTER TABLE RSVPCheckBoxListQuestionTemplate ADD CONSTRAINT FK_CE1F6211D3633A FOREIGN KEY (ID) REFERENCES RSVPQuestionTemplate (ID) ON DELETE CASCADE;     
ALTER TABLE RSVPTemplate ADD CONSTRAINT FK_87A5AC1CCABFF699 FOREIGN KEY (CreatedByID) REFERENCES Member (ID);     
ALTER TABLE RSVPTemplate ADD CONSTRAINT FK_87A5AC1C90CF7278 FOREIGN KEY (SummitID) REFERENCES Summit (ID);     
ALTER TABLE RSVPMemberLastNameQuestionTemplate ADD CONSTRAINT FK_83792E5E11D3633A FOREIGN KEY (ID) REFERENCES RSVPQuestionTemplate (ID) ON DELETE CASCADE;     
ALTER TABLE RSVPRadioButtonListQuestionTemplate ADD CONSTRAINT FK_CB2D9F1611D3633A FOREIGN KEY (ID) REFERENCES RSVPQuestionTemplate (ID) ON DELETE CASCADE;     
ALTER TABLE RSVPMemberEmailQuestionTemplate ADD CONSTRAINT FK_2CCDB0FA11D3633A FOREIGN KEY (ID) REFERENCES RSVPQuestionTemplate (ID) ON DELETE CASCADE;     
UPDATE RSVPMultiValueQuestionTemplate SET DefaultValueID = NULL
WHERE  NOT EXISTS (SELECT ID FROM RSVPQuestionValueTemplate WHERE ID = DefaultValueID);
ALTER TABLE RSVPMultiValueQuestionTemplate ADD CONSTRAINT FK_DFF16B50D3F2F9E8 FOREIGN KEY (DefaultValueID) REFERENCES RSVPQuestionValueTemplate (ID);          
SQL;

        $statements = explode(';', $sql);
        foreach($statements as $statement){
            $statement = trim($statement);
            if(empty($statement)) continue;
            DB::query($statement);
        }

    }
}