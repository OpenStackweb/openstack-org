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
final class SetPresentationEventType extends MigrationTask
{
    protected $title = "Set Presentation Event Type";

    protected $description = 'Create EventType "Presentation" if not present and assing this type to all current presentations';

    function up()
    {
        echo "Starting Migration Proc ...<BR>";
        //check if migration already had ran ...
        $migration = Migration::get()->filter('Name', $this->title)->first();

        if (!$migration) {

            $presentation_type = SummitEventType::get("SummitEventType","Type = 'Presentation' AND SummitID = 5")->first();

            if ($presentation_type) {
                $presentation_type_id = $presentation_type->ID;
            } else {
                $presentation_type = new SummitEventType();
                $presentation_type->Type = 'Presentation';
                $presentation_type->SummitID = 5;
                $presentation_type->Color = '#D0A9F5';
                $presentation_type_id = $presentation_type->Write();
            }

            $SQL = <<<SQL
                UPDATE SummitEvent SET TypeID = $presentation_type_id WHERE ClassName = 'Presentation' AND SummitID = 5;
SQL;

            DB::query($SQL);


            $migration = new Migration();
            $migration->Name = $this->title;
            $migration->Description = $this->description;
            $migration->Write();
        }
        echo "Ending  Migration Proc ...<BR>";
    }

    function down()
    {

    }

}