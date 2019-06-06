<?php
/**
 * Copyright 2019 Openstack Foundation
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
 * Class UpdateTechnicalCommitteeTask
 */

use GuzzleHttp\Client as HttpClient;

final class UpdateTechnicalCommitteeTask extends CronTask
{

    function run()
    {

        $client = new HttpClient([
            'defaults' => [
                'timeout'         => 60,
                'allow_redirects' => false,
                'verify'          => true
            ]
        ]);

        try {

            $url = 'https://opendev.org/openstack/governance/raw/branch/master/reference/members.yaml';
            $yamlResponse = $client->get($url);

            if (is_null($yamlResponse)) exit();
            if ($yamlResponse->getStatusCode() != 200) exit();
            $body = $yamlResponse->getBody();
            if (is_null($body)) exit();
            $content = $body->getContents();
            if (empty($content)) exit();

            $memberYaml = Spyc::YAMLLoadString($content);


            $group = Group::get()->filter('Code', 'technical-committee' )->first();
            $group->Members()->removeAll();

            foreach ($memberYaml as $member) {
                if(!isset($member['memberid'])) continue;

                $memberObj = Member::get()->byID($member['memberid']);

                if (!$memberObj) continue;

                $group->Members()->add($memberObj);
            }

            $group->write();

            return 'OK';
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            echo $ex->getMessage();
        }
    }
} 