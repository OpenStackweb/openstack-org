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

final class AssetsSyncRequestProcessorTask extends CronTask
{

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * AssetsSyncRequestProcessorTask constructor.
     * @param ITransactionManager $tx_manager
     */
    public function __construct(ITransactionManager $tx_manager){
        $this->tx_manager = $tx_manager;
        parent::__construct();
    }

    /**
     * @return void
     */
    public function run()
    {
        try
        {
            $init_time   = time();
            $processed   = $this->tx_manager->transaction(function(){
                $processed = 0;
                $requests  = AssetsSyncRequest::get()->filter([
                    'Processed' => 0
                ])->sort('ID', 'ASC');

                foreach($requests as $sync_request){
                    if(file_exists($sync_request->Origin)){
                        echo sprintf("file %s exists!", $sync_request->Origin).PHP_EOL;
                        $destination = sprintf("%s/%s", ASSETS_PATH, $sync_request->Destination);
                        echo sprintf("trying to copying from %s to %s ...", $sync_request->Origin, $destination).PHP_EOL;
                        $res         = copy($sync_request->Origin,  $destination);
                        if(!$res){
                            echo sprintf("error copying file from %s to %s", $sync_request->Origin, $destination).PHP_EOL;
                            continue;
                        }
                        echo sprintf("deleting file %s ...", $sync_request->Origin).PHP_EOL;
                        $res = unlink($sync_request->Origin);
                        chown($destination, 'www-data');
                        if(!$res){
                            echo sprintf("error removing file from %s", $sync_request->Origin).PHP_EOL;
                        }
                    }
                    else{
                        echo sprintf("file %s not exists!", $sync_request->Origin).PHP_EOL;
                    }
                    $sync_request->markAsProcessed();
                    $sync_request->write();
                    $processed++;
                }

                return $processed;
            });
            $finish_time = time() - $init_time;
            echo 'processed records ' . $processed. ' - time elapsed : '.$finish_time. ' seconds.';
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
        }
    }
}