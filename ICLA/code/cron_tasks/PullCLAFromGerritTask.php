<?php
/**
 * Copyright 2014 Openstack Foundation
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
 * Class PullCLAFromGerritTask
 */
final class PullCLAFromGerritTask extends CronTask {

	function run(){

        $init_time  = time();

		$manager = new ICLAManager (
			new GerritAPI(GERRIT_BASE_URL, GERRIT_USER, GERRIT_PASSWORD),
			new SapphireGerritUserRepository,
			SapphireTransactionManager::getInstance()
		);

		$members_updated = $manager->processICLAGroup(ICLA_GROUP_ID);

		echo sprintf("Gerrit Users Processed %s .", $members_updated).PHP_EOL;
        $finish_time = time() - $init_time;
        echo 'time elapsed : '.$finish_time. ' seconds.'.PHP_EOL;
	}
} 