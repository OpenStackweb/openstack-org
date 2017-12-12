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

/**
 * Class EmailCreationRequestProcessTask
 */
final class EmailCreationRequestProcessTask extends CronTask
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
                $requests  = EmailCreationRequest::get()->filter([
                    'Processed' => 0
                ])->sort('ID', 'ASC');

                foreach($requests as $email_request){
                    switch($email_request->ClassName){
                        case "SpeakerCreationEmailCreationRequest":{
                            $sender = new PresentationSpeakerCreationEmailMessageSender;
                            $sender->send(['Speaker' => $email_request->Speaker()]);
                        }
                        break;
                        default:
                        {
                            continue;
                        }
                        break;
                    }

                    $email_request->markAsProcessed();
                    $email_request->write();
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