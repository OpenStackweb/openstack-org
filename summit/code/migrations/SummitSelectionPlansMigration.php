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
final class SummitSelectionPlansMigration extends AbstractDBMigrationTask
{
    protected $title = "SummitSelectionPlansMigration";

    protected $description = "SummitSelectionPlansMigration";

    function doUp()
    {
        global $database;


        $res = DB::query("SELECT * FROM PresentationCategoryGroup;");

        foreach($res as $row) {
            $summit = Summit::get()->byID($row['SummitID']);
            $summit_row = DB::query("SELECT * FROM Summit WHERE ID = ".$summit->ID)->first();
            $cat_group = PresentationCategoryGroup::get()->byID($row['ID']);

            if($summit->ID < 24) {
                $plan = $summit->SelectionPlans()->First();
                if ( !$plan ) {
                    $plan = new SelectionPlan();
                    $plan->SummitID = $summit->ID;
                    $plan->Name = 'Selection Plan 1';
                    $plan->SubmissionBeginDate = $summit_row['SubmissionBeginDate'];
                    $plan->SubmissionEndDate = $summit_row['SubmissionEndDate'];
                    $plan->VotingBeginDate = $summit_row['VotingBeginDate'];
                    $plan->VotingEndDate = $summit_row['VotingEndDate'];
                    $plan->SelectionBeginDate = $summit_row['SelectionBeginDate'];
                    $plan->SelectionEndDate = $summit_row['SelectionEndDate'];
                    $plan->write();
                }
            } else {
                $plan = new SelectionPlan();
                $plan->SummitID = $summit->ID;
                $plan->Name = $cat_group->Name;
                $plan->SubmissionBeginDate = $summit_row['SubmissionBeginDate'];
                $plan->SubmissionEndDate = $summit_row['SubmissionEndDate'];
                $plan->VotingBeginDate = $summit_row['VotingBeginDate'];
                $plan->VotingEndDate = $summit_row['VotingEndDate'];
                $plan->SelectionBeginDate = $summit_row['SelectionBeginDate'];
                $plan->SelectionEndDate = $summit_row['SelectionEndDate'];

                if ($plan->validate()) {
                    $plan->Enabled = false;
                }

                $plan->write();
            }

            $plan->CategoryGroups()->add($cat_group);
        }

        DB::query("ALTER TABLE Summit DROP COLUMN `SubmissionBeginDate`");
        DB::query("ALTER TABLE Summit DROP COLUMN `SubmissionEndDate`");
        DB::query("ALTER TABLE Summit DROP COLUMN `VotingBeginDate`");
        DB::query("ALTER TABLE Summit DROP COLUMN `VotingEndDate`");
        DB::query("ALTER TABLE Summit DROP COLUMN `SelectionBeginDate`");
        DB::query("ALTER TABLE Summit DROP COLUMN `SelectionEndDate`");

    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}