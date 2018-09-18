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
 * Class Survey2017To2018Migration
 */
final class Survey2017To2018Migration extends AbstractDBMigrationTask
{
    protected $title = "Survey2017To2018Migration";

    protected $description = "Survey2017To2018Migration";

    function doUp()
    {
        $query_sql = <<<SQL
SELECT * FROM Survey AS OLD_SURVEY where
OLD_SURVEY.TemplateID = 10
AND OLD_SURVEY.Created BETWEEN '2017-08-14 00:00:00' AND '2018-03-30 23:59:59'
AND NOT EXISTS (SELECT 1 FROM Survey where Survey.TemplateID = 12 and Survey.CreatedByID = OLD_SURVEY.CreatedByID);
SQL;

        $survey_manager = Injector::inst()->get("SurveyManager");
        $old_surveys_rows = DB::query($query_sql);
        $current_template = SurveyTemplate::get()->byID(12);
        foreach($old_surveys_rows as $row){
            $old_survey = new Survey($row);
            $new_survey = $survey_manager->buildSurvey
            (
                $current_template->getIdentifier(),
                $old_survey->CreatedByID
            );

            if($current_template->shouldPrepopulateWithFormerData())
            {
                $survey_manager->doAutopopulation
                (
                    $new_survey,
                    SurveyDataAutoPopulationStrategyFactory::build(SurveyDataAutoPopulationStrategyFactory::NEW_STRATEGY)
                );
            }

            if($old_survey->isComplete())
                $new_survey->markComplete();

            $new_survey->write();

            DB::query(sprintf("UPDATE Survey SET Created='2018-08-20 00:00:00', LastEdited = '2018-08-20 00:00:00' WHERE ID = %s", $new_survey->ID));

        }
    }
}