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
final class LoadInitialCOADump extends CronTask
{
    protected $title = "LoadInitialCOADump";

    protected $description = "LoadInitialCOADump";

    /**
     * @var ICOAManager
     */
    private $manager;
    /**
     * IngestCOAFilesTask constructor.
     * @param ICOAManager $manager
     */
    public function __construct(ICOAManager $manager){
        $this->manager = $manager;
        parent::__construct();
    }

    function run()
    {
        $filename = isset($_GET['filename']) ? trim($_GET['filename']) : null;
        if (is_null($filename)) {
            echo 'ERROR - csv_file param missing!';
            exit;
        }

        $this->manager->processExternalInitialDump($filename);
    }

}