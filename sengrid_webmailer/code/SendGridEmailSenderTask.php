<?php

/**
 * Copyright 2016 OpenStack Foundation
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
final class SendGridEmailSenderTask extends CronTask
{

    /**
     * @return void
     */
    public function run()
    {
        try
        {

            $init_time  = time();
            $batch_size = 100;

            if (isset($_GET['batch_size']))
            {
                $batch_size = intval(trim(Convert::raw2sql($_GET['batch_size'])));
                echo sprintf('batch_size set to %s', $batch_size).PHP_EOL;
            }

            $processed = SapphireTransactionManager::getInstance()->transaction(function() use($batch_size ){

                $emails           = SentEmailSendGrid::get()->filter(['IsSent' => 0])->sort('Created', 'ASC')->limit($batch_size, 0);
                $send_grid_mailer = new SendGridWebMailer(null);
                $counter          = 0;

                foreach($emails as $email){

                    $is_plain   = $email->IsPlain;
                    $to          = $email->To;
                    $from        = $email->From;
                    $subject     = $email->Subject;
                    $body        = $email->Body;
                    $attachments = $email->Attachments;

                    if(!empty($attachments)){
                        $attachments = json_decode($attachments);
                    }
                    else{
                        $attachments = false;
                    }

                    if($is_plain){
                        $send_grid_mailer->sendPlain($to, $from, $subject, $body, $attachments);
                    }
                    else{
                        $send_grid_mailer->sendHTML($to, $from, $subject, $body, $attachments);
                    }

                    $email->markAsSent()->write();
                    ++$counter;
                }

                return $counter;
            });

            $finish_time = time() - $init_time;
            echo 'processed records ' . $processed. ' - time elapsed : '.$finish_time. ' seconds.'.PHP_EOL;
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
        }
    }
}