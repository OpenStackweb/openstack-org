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
final class SummitSpeakerPromoCodesIngestTask extends CronTask {

    protected $title = "Summit Promo Registration Codes Ingest Task";

    protected $description = "Loads the promo registration codes for current summit from assets folder";

    /**
     * @return void
     */
    public function run()
    {

        $promo_code_type = isset($_GET['promo_code_type'])? intval($_GET['promo_code_type']):null;
        $promo_code_file = isset($_GET['promo_code_file'])?$_GET['promo_code_file']:null;
        $summit_id       = isset($_GET['summit_id'])?$_GET['summit_id']:null;
        if(is_null($promo_code_type))
        {
            echo 'ERROR - promo_code_type param missing!';
            exit;
        }
        if(is_null($promo_code_file)){
            echo 'ERROR - promo_code_file param missing!';
            exit;
        }
        if(is_null($summit_id )){
            echo 'ERROR - summit_id param missing!';
            exit;
        }

        $base_path = ASSETS_PATH;
        $file_path = $base_path.'/'.$promo_code_file;

        $type = explode(".", $file_path);
        if(!strtolower(end($type)) == 'csv'){
            echo 'ERROR - file hast not a csv extension!';
            exit;
        }

        if(!file_exists($file_path))
        {
            echo sprintf('ERROR - %s file does not exists!', $file_path);
            exit;
        }

        $summit = Summit::get()->byID(intval($summit_id));

        if(is_null($summit))
        {
            echo 'ERROR - summit does not exists!';
            exit;
        }

        $reader = new CSVReader($file_path);
        $row = 0;
        do
        {
            $line = $reader->getLine();
            if($line)
            {
                ++$row;
                if($row === 1) continue; // skip header ...
                switch($promo_code_type)
                {
                    case 1:
                        $type = 'ACCEPTED';
                        break;
                    case 2:
                        $type = 'ALTERNATE';
                        break;
                }

                $code           = new SpeakerSummitRegistrationPromoCode;
                $code->Code     = $line[0];
                $code->Type     = $type;
                $code->SummitID = $summit->ID;

                try
                {
                    $code->write();
                }
                catch(Exception $ex)
                {
                    SS_Log::log($ex->getMessage(), SS_Log::ERR);
                }
            }
        }
        while($line);
    }
}