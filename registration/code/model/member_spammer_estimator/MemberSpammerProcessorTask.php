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
use  Symfony\Component\Process\Process;
/**
 * Class MemberSpammerProcessorTask
 */
final class MemberSpammerProcessorTask extends CronTask
{

    protected $title = "MemberSpammerProcessorTask";

    protected $description = "MemberSpammerProcessorTask";

    /**
     * @throws Exception
     */
    public function run()
    {
        SapphireTransactionManager::getInstance()->transaction(function(){
            $command = sprintf( '%1$s/registration/code/model/member_spammer_estimator/member_spammer_estimator_process.sh "%1$s/registration/code/model/member_spammer_estimator" "%1$s"', Director::baseFolder());
            $process = new Process($command);
            $process->setTimeout(PHP_INT_MAX);
            $process->setIdleTimeout(PHP_INT_MAX);
            $process->run();

            while ($process->isRunning()) {
            }

            $csv_content = $process->getOutput();
            echo $csv_content.PHP_EOL;

            if (!$process->isSuccessful()) {
                throw new Exception("Process Error!");
            }

            $rows   = CSVReader::load($csv_content);
            $output = "<p>Nothing to process</p>";

            if(count($rows) > 0)
            {   $output = '<ul>';

                foreach($rows as $row){
                    $member_id = intval($row["ID"]);
                    $type      = $row["Type"];
                    $member    = Member::get()->byID($member_id);

                    if(!$member) continue;
                    // member processing
                    if($type == 'Spam'){
                        echo sprintf("Marking Member %s as Spam", $member->Email).PHP_EOL;
                        $member->deActivate();
                    }
                    else{
                        echo sprintf("Marking Member %s as Ham", $member->Email).PHP_EOL;
                        $member->activate();
                    }

                    $member->write();

                    $action_url  = $type == "Ham"? "/members-spammers/%s/deactivate": "/members-spammers/%s/activate";
                    $action_url  = Director::absoluteURL(sprintf($action_url, $member->ID));
                    $action_text = $type == "Ham"? "Mark as Spam": "Mark as Ham";
                    $edit_url    = Director::absoluteURL(sprintf("/admin/security/EditForm/field/Members/item/%s/edit", $member->ID));

                    $output .= sprintf(
                        "<li>[%s] - %s, %s (%s).<a href='%s'>Edit</a> <a href='%s'>%s</a></li>",
                        $row["Type"],
                        $member->FirstName,
                        $member->Surname,
                        $member->Email,
                        $edit_url,
                        $action_url,
                        $action_text
                    );


                }
                $output .= '</ul>';
            }

            $email = EmailFactory::getInstance()->buildEmail
            (
                "noreply@openstack.org",
                MEMBER_SPAM_PROCESSOR_TO,
                "Member Spammer Processor Task Results",
                $output
            );

            $email->send();
        });
    }
}