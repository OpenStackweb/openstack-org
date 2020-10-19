<?php
/**
 * Copyright 2017 Open Infrastructure Foundation
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
 * Class GitHubPullRequestProcessorTask
 */
final class GitHubPullRequestProcessorTask extends CronTask
{

    /**
     * @var IPullRequestManager
     */
    private $manager;

    /**
     * GitHubPullRequestProcessorTask constructor.
     * @param IPullRequestManager $manager
     */
    public function __construct(IPullRequestManager $manager)
    {
        parent::__construct();
        $this->manager = $manager;
    }

    /**
     * @return void
     */
    public function run()
    {

        try{

            $batch_size = 100;
            if(isset($_GET['batch_size'])){
                $batch_size = intval(trim(Convert::raw2sql($_GET['batch_size'])));
            }

            $res = $this->manager->processPullRequests($batch_size);

            echo sprintf("processed records %s", $res).PHP_EOL;

            return 'OK';
        }
        catch(Exception $ex){
            SS_Log::log($ex,SS_Log::ERR);
            echo $ex->getMessage();
        }
    }
}