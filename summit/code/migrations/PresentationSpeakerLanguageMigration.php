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
 * Class PresentationSpeakerLanguageMigration
 */
final class PresentationSpeakerLanguageMigration extends AbstractDBMigrationTask
{
    protected $title = "PresentationSpeakerLanguageMigration";

    protected $description = "PresentationSpeakerLanguageMigration";

    function doUp()
    {
        global $database;

        foreach(DB::query("SELECT * from SpeakerLanguage;") as $row){
            $speaker = PresentationSpeaker::get()->byID(intval($row['SpeakerID']));
            if(!$speaker) continue;
            $language = Language::get()->filter(['Name' => trim($row['Language'])])->first();
            if(!$language) continue;
            $speaker->Languages()->add($language);
        }

        DB::query("DROP TABLE SpeakerLanguage");

    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}