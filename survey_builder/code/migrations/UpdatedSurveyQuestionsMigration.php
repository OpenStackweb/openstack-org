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

class UpdatedSurveyQuestionsMigration extends AbstractDBMigrationTask
{
    protected $title = "UpdatedSurveyQuestionsMigration";

    protected $description = "UpdatedSurveyQuestionsMigration";

    function doUp()
    {
        $sql = <<<SQL
UPDATE SurveyMultiValueQuestionTemplate SET DefaultGroupLabel = '<p>Others</p>';

INSERT INTO SurveyRadioButtonListQuestionTemplate (ID, Orientation)
SELECT ID, 'Vertical' FROM SurveyQuestionTemplate WHERE ClassName = 'SurveyRadioButtonListQuestionTemplate';
SQL;

        $statements = explode(';', $sql);
        foreach($statements as $statement){
            $statement = trim($statement);
            if(empty($statement)) continue;
            DB::query($statement);
        }
    }

    function doDown()
    {

    }
}