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

class GerritApiTest extends SapphireTest
{

    public function tearDown()
    {
        Mockery::close();
    }

    public function testGetUserCommits(){
        $client = new GerritAPI(GERRIT_BASE_URL, GERRIT_USER, GERRIT_PASSWORD);
        $account_id = 9139;
        $start      = 0;
        $response   = $client->getUserCommits( $account_id,GerritChangeStatus::_MERGED, 10, $start);
        $this->assertTrue(count($response) > 0);
    }
}