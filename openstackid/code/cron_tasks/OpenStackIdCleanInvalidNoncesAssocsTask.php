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
 * Class OpenStackIdCleanInvalidNoncesAssocsTask
 */
final class OpenStackIdCleanInvalidNoncesAssocsTask extends CronTask
{

    /**
     * @var Auth_OpenID_OpenIDStore
     */
    private $openid_repository;

    public function __construct(Auth_OpenID_OpenIDStore $openid_repository)
    {
        parent::__construct();
        $this->openid_repository = $openid_repository;
    }

    /**
     * @return void
     */
    public function run()
    {
        try
        {
            $init_time  = time();
            list($nonces_expired, $assoc_expired) = $this->openid_repository->cleanup();
            $finish_time = time() - $init_time;
            echo "nonces expired {$nonces_expired}".PHP_EOL;
            echo "associations expired {$assoc_expired}".PHP_EOL;
            echo "time elapsed : {$finish_time} seconds.".PHP_EOL;
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
        }
    }
}