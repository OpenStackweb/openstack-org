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
class SurveyStateMigration extends AbstractDBMigrationTask
{
    protected $title = "Survey State Migration";

    protected $description = "Update the survey state for past surveys";

    function doUp()
    {
        global $database;

        DB::query("UPDATE Survey
                   INNER JOIN
                   ( SELECT S.ID FROM Survey S
                     LEFT JOIN SurveyStep SS ON SS.ID = S.MaxAllowedStepID
                     LEFT JOIN SurveyTemplate ST ON ST.ID = S.TemplateID
                     LEFT JOIN SurveyStepTemplate SST ON SST.SurveyTemplateID = ST.ID AND SST.ID = (SELECT ID FROM SurveyStepTemplate SST2 WHERE SST2.SurveyTemplateID = ST.ID ORDER BY `Order` ASC LIMIT 1)
                     WHERE SST.ID = SS.TemplateID AND S.TemplateID < 6
                   ) AS Q1 ON Survey.ID = Q1.ID
                   SET State = 'COMPLETE'");
    }

    function doDown()
    {

    }
}