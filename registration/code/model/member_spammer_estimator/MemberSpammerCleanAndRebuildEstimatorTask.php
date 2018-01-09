<?php
/**
 * Copyright 2017 OpenStack Foundation
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

final class MemberSpammerCleanAndRebuildEstimatorTask extends CronTask
{

    protected $title = "MemberSpammerCleanAndRebuildEstimatorTask";

    protected $description = "MemberSpammerCleanAndRebuildEstimatorTask";
    /**
     * @return void
     */
    public function run()
    {
        echo "truncating MemberEstimatorFeed".PHP_EOL;

        DB::query("TRUNCATE TABLE MemberEstimatorFeed;");

        echo "inserting initial spammer seeding".PHP_EOL;
        $json_content = file_get_contents(Director::baseFolder().  "/registration/code/model/member_spammer_estimator/initial_spam_feed.json");
        $rows         = json_decode($json_content, true );
        foreach ($rows as $row){
            DB::query(sprintf("INSERT INTO MemberEstimatorFeed
            (FirstName, Surname, Email, Bio, `Type`)
            VALUES('%s', '%s', '%s', '%s', '%s');",
                $row['FirstName'],
                $row['Surname'],
                $row['Email'],
                str_replace("'", "\"", $row['Bio']),
                "Spam"
            ));
        }

        echo "inserting initial non spammer seeding".PHP_EOL;
        db::query("INSERT INTO MemberEstimatorFeed
(FirstName, Surname, Email, Type)
SELECT FirstName, Surname, Email, Type FROM Member 
WHERE Member.Type = 'Ham' LIMIT 0, 300;");

    }
}