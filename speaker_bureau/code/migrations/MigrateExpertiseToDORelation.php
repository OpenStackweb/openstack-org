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
final class MigrateExpertiseToDORelation extends AbstractDBMigrationTask
{
    protected $title = "Speaker Expertise migrate to has_many rel";

    protected $description = "Move the Expertise field populated as text field with break lines, onto a has_many relation in new DO.";

    function doUp()
    {
        $speakers = DB::query("SELECT * FROM PresentationSpeaker WHERE Expertise IS NOT NULL");

        foreach ($speakers as $speaker) {
            $expertise_array = preg_split ('/$\R?^/m', $speaker['Expertise']);
            foreach ($expertise_array as $exp) {
                $new_exp = new SpeakerExpertise();
                $new_exp->Expertise = $exp;
                $new_exp->SpeakerID = $speaker['ID'];

                $new_exp->Write();
            }
        }

        DB::query("ALTER TABLE PresentationSpeaker DROP COLUMN Expertise");
    }

    function doDown()
    {

    }
}