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
 * Class UpdateTrackTemplateQuestionIntegrityMigration
 */
final class UpdateTrackTemplateQuestionIntegrityMigration extends AbstractDBMigrationTask
{

    protected $title = "UpdateTrackTemplateQuestionIntegrityMigration";

    protected $description = "UpdateTrackTemplateQuestionIntegrityMigration";

    function doUp()
    {
        $sql = <<<SQL
DELETE FROM TrackQuestionTemplate WHERE ClassName = 'TrackQuestionTemplate';
DELETE FROM TrackSingleValueTemplateQuestion WHERE NOT EXISTS ( SELECT 1 FROM TrackQuestionTemplate WHERE TrackQuestionTemplate.ID = TrackSingleValueTemplateQuestion.ID);
ALTER TABLE TrackSingleValueTemplateQuestion ADD CONSTRAINT FK_20DDFA511D363BA FOREIGN KEY (ID) REFERENCES TrackQuestionTemplate (ID) ON DELETE CASCADE;
DELETE FROM TrackTextBoxQuestionTemplate WHERE NOT EXISTS ( SELECT 1 FROM TrackQuestionTemplate WHERE TrackQuestionTemplate.ID = TrackTextBoxQuestionTemplate.ID);     
ALTER TABLE TrackTextBoxQuestionTemplate ADD CONSTRAINT FK_B23A250311D363CA FOREIGN KEY (ID) REFERENCES TrackQuestionTemplate (ID) ON DELETE CASCADE;
DELETE FROM TrackRadioButtonListQuestionTemplate WHERE NOT EXISTS ( SELECT 1 FROM TrackQuestionTemplate WHERE TrackQuestionTemplate.ID = TrackRadioButtonListQuestionTemplate.ID);     
ALTER TABLE TrackRadioButtonListQuestionTemplate ADD CONSTRAINT FK_A4653D7111D363QQ FOREIGN KEY (ID) REFERENCES TrackQuestionTemplate (ID) ON DELETE CASCADE;
DELETE FROM TrackMultiValueQuestionTemplate WHERE NOT EXISTS ( SELECT 1 FROM TrackQuestionTemplate WHERE TrackQuestionTemplate.ID = TrackMultiValueQuestionTemplate.ID);     
ALTER TABLE TrackMultiValueQuestionTemplate ADD CONSTRAINT FK_750444E611D363JK FOREIGN KEY (ID) REFERENCES TrackQuestionTemplate (ID) ON DELETE CASCADE;
DELETE FROM TrackLiteralContentQuestionTemplate WHERE NOT EXISTS ( SELECT 1 FROM TrackQuestionTemplate WHERE TrackQuestionTemplate.ID = TrackLiteralContentQuestionTemplate.ID);     
ALTER TABLE TrackLiteralContentQuestionTemplate ADD CONSTRAINT FK_8865FD4E11D363JKI FOREIGN KEY (ID) REFERENCES TrackQuestionTemplate (ID) ON DELETE CASCADE;
DELETE FROM TrackDropDownQuestionTemplate WHERE NOT EXISTS ( SELECT 1 FROM TrackQuestionTemplate WHERE TrackQuestionTemplate.ID = TrackDropDownQuestionTemplate.ID);     
ALTER TABLE TrackDropDownQuestionTemplate ADD CONSTRAINT FK_DFF16B5011D363MAR FOREIGN KEY (ID) REFERENCES TrackQuestionTemplate (ID) ON DELETE CASCADE;     
DELETE FROM TrackCheckBoxQuestionTemplate WHERE NOT EXISTS ( SELECT 1 FROM TrackQuestionTemplate WHERE TrackQuestionTemplate.ID = TrackCheckBoxQuestionTemplate.ID);
ALTER TABLE TrackCheckBoxQuestionTemplate ADD CONSTRAINT FK_CE1F6211D363CAC FOREIGN KEY (ID) REFERENCES TrackQuestionTemplate (ID) ON DELETE CASCADE;          
DELETE FROM TrackCheckBoxListQuestionTemplate WHERE NOT EXISTS ( SELECT 1 FROM TrackQuestionTemplate WHERE TrackQuestionTemplate.ID = TrackCheckBoxListQuestionTemplate.ID);
ALTER TABLE TrackCheckBoxListQuestionTemplate ADD CONSTRAINT FK_83792E5E11D363TEX FOREIGN KEY (ID) REFERENCES TrackQuestionTemplate (ID) ON DELETE CASCADE;          
     
SQL;

        $statements = explode(';', $sql);
        foreach($statements as $statement){
            $statement = trim($statement);
            if(empty($statement)) continue;
            DB::query($statement);
        }

    }
}