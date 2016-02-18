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
final class DBMigrateTask extends MigrationTask
{
    protected $title = "DB migration task";

    protected $description = "DB migration task";

    private static $migrations = array
    (
        'SchedRefactoringMigration',
        'SchedRefactoringMigration2',
        'CreateTableCountries',
        'MigrateExpertiseToDORelation',
        'ChangeTagsCollationMigration',
        'UpdateSurveyTemplateIndexMigration',
        'UpdateAttendeesTicketsMigration2',
        'RemovePresentationDeprecatedColumnsMigration',
        'MemberEmailVerificationMigration',
        'SummitEventbriteRegistrationEmailMigration',
    );

    function up()
    {
        echo "Starting DB Migration Proc ...".PHP_EOL;
        if(count(self::$migrations))
        {
            echo sprintf("found %s migrations to run ...", count(self::$migrations)).PHP_EOL;

            foreach(self::$migrations as $migration_class){
                $m = new $migration_class;
                if($m instanceof MigrationTask)
                {
                    echo "running migration ".$migration_class.PHP_EOL;
                    $m->up();
                }
            }
        }
        echo "Ending DB Migration Proc ...".PHP_EOL;
    }

}