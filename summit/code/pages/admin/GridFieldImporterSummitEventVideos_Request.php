<?php
/**
 * Copyright 2020 OpenStack Foundation
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
 * Class GridFieldImporterSummitEventVideos_Request
 */
class GridFieldImporterSummitEventVideos_Request extends GridFieldImporter_Request
{

    /**
     * @var ScheduleManager
     */
    private $manager;

    public function __construct(GridField $gridField, GridField_URLHandler $component, RequestHandler $handler)
    {
        parent::__construct($gridField, $component, $handler);
        $this->manager = Injector::inst()->get('ScheduleManager');
    }

    public function import($file_path)
    {
        global $email_from;
        global $email_log;

        try {
            $file_name = Director::baseFolder() . $file_path;
            if (filter_var($file_path, FILTER_VALIDATE_URL)) {
                // download it
                $file_name =  '/tmp/'.random_string(16).'.csv';
                file_put_contents( $file_name, file_get_contents($file_path));
            }

            $this->manager->ingestVideosFromCSV($file_name, intval($this->getRequest()->param("ID")));

        }
        catch (Exception $ex){
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            throw $ex;
        }
    }
}