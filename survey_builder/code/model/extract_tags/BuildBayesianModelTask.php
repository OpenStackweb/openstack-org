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
use Symfony\Component\Process\Process;

/**
 * Class BuildBayesianModelTask
 * this class run the Bayesian estimator builder
 * for free text questions
 */
final class BuildBayesianModelTask extends CronTask
{

    /**
     * @throws Exception
     */
    public function run()
    {
        $model_folder = Director::baseFolder().'/survey_builder/code/model/extract_tags/bayesian_models';
        $command = sprintf( ' %s/survey_builder/code/model/extract_tags/bayesian_model_builder.sh "%s/survey_builder/code/model/extract_tags" "%s"', Director::baseFolder(),  Director::baseFolder(), $model_folder);
        $process = new Process($command);
        $process->setWorkingDirectory(sprintf('%s/survey_builder/code/model/extract_tags', Director::baseFolder()));
        $process->setTimeout(PHP_INT_MAX);
        $process->setIdleTimeout(PHP_INT_MAX);
        $process->run();

        while ($process->isRunning()) {
        }

        $output = $process->getOutput();

        echo $output.PHP_EOL;

        if (!$process->isSuccessful()) {
            throw new Exception();
        }

    }
}