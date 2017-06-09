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
use GO\Scheduler;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class SchedulerCronTask
 */
final class SchedulerCronTask extends CronTask
{

    /**
     * @return void
     */
    public function run()
    {
        // Create a new scheduler
        $scheduler   = new Scheduler();
        $base_folder = Director::baseFolder();
        $path        = $base_folder. '/cron_jobs_scheduler/_config/schedule.yml';

        try {

            echo "reading jobs list from " . $path . ' ...' . PHP_EOL;
            $yaml = Yaml::parse(file_get_contents($path));

            if (!is_null($yaml) && isset($yaml['jobs']) && count($yaml['jobs'])) {

                echo sprintf("found %s jobs to run ...", count($yaml['jobs'])).PHP_EOL;

                foreach ($yaml['jobs'] as $index => $job) {

                    if(!isset($job['name']) || !isset($job['cron_expression']) || !isset($job['enabled'])){
                        echo "job name or cron_expression is not set ... skipping it !".PHP_EOL;
                        continue;
                    }

                    $name       = $job['name'];
                    $expression = $job['cron_expression'];
                    $enabled    = intval($job['enabled']);

                    if($enabled == 0){
                        echo sprintf("job %s is disabled! skipping it ...", $name).PHP_EOL;
                        continue;
                    }

                    $params     = isset($job['params']) ? $job['params'] : '';
                    $raw        = sprintf('cd %s && sake %s %s', $base_folder, $name, $params);

                    echo sprintf('running %s at %s', $raw, $expression) . PHP_EOL;
                    $scheduler->raw($raw)->at($expression)->onlyOne()->output(sprintf("/tmp/%s.log", strtolower($name)));
                }
                $scheduler->run();
            }
        }
        catch(Exception $ex){
            echo $ex->getMessage().PHP_EOL;
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return -1;
        }
    }
}