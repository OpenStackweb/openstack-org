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
        $command = sprintf( '%1$s/registration/code/model/member_spammer_estimator/member_spammer_estimator_process.sh "%1$s/registration/code/model/member_spammer_estimator" "%1$s"', Director::baseFolder());
        $process = new Process($command);
        $process->setTimeout(PHP_INT_MAX);
        $process->setIdleTimeout(PHP_INT_MAX);
        $process->run();

        while ($process->isRunning()) {
        }

        $output = $process->getOutput();
        echo $output.PHP_EOL;

        if (!$process->isSuccessful()) {
            throw new Exception("Process Error!");
        }

        $email = EmailFactory::getInstance()->buildEmail
        (
            "noreply@openstack.org",
            MEMBER_SPAM_PROCESSOR_TO,
            "Member Spammer Processor Task Results",
            $output
        );

        $email->send();
    }
}