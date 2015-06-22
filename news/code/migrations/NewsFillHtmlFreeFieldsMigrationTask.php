<?php
/**
 * Copyright 2014 Openstack Foundation
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
 * Class ElectionMigrationTask
 */
final class NewsFillHtmlFreeFieldsMigrationTask extends MigrationTask {

    protected $title = "Fill News Html Free Fields Migration";

    protected $description = "Fill SummaryHtmlFree and BodyHtmlFree fields with html stripped versions of summary and body";

    function up(){
        echo "Starting Migration Proc ...<BR>";
        //check if migration already had ran ...
        $migration = DataObject::get_one("Migration", "Name='{$this->title}'");
        if (!$migration) {

            $result = News::get();
            foreach($result as $news) {
                $news->SummaryHtmlFree   = strip_tags($news->Summary);
                $news->BodyHtmlFree   = strip_tags($news->Body);
                $news->Write();
            }
        }
        echo "Ending  Migration Proc ...<BR>";
    }
}