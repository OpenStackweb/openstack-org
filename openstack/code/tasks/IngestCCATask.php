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
 * Class IngestCCATask
 */
final class IngestCCATask extends CronTask {

	function run(){

        global $database;

        $memberRepository = new SapphireMemberRepository();
        $ccaPath = Director::baseFolder() . '/openstack/code/tasks/data/cca.csv';

		try{
            $ds = new CvsDataSourceReader(",");
            $ds->Open($ccaPath);
            $headers = $ds->getFieldsInfo();
            $contributor = array();
            $member = null;
            $contributorObj = null;

            while (($row = $ds->getNextRow()) !== FALSE) {
                $contributor['first_name'] = $row[$headers["First Name"]];
                $contributor['last_name'] = $row[$headers["Last Name"]];
                $contributor['email'] = $row[$headers["Email"]];
                $contributor['awards'] = $row[$headers["Awards"]];

                echo print_r($contributor);

                //echo 'processing contributor ' . $contributor['last_name'] . PHP_EOL;

                if ($email = $contributor['email']) {
                    $email = rtrim($email, ", ");
                    $contributorObj = CommunityContributor::get()->filter('Email', $email)->first();
                }

                if (!$contributorObj && $contributor['first_name'] && $contributor['last_name']) {
                    $contributorObj = CommunityContributor::get()
                        ->filter(['FirstName' => $contributor['first_name'], 'LastName' => $contributor['last_name']])->first();
                }

                if (!$contributorObj) {
                    $contributorObj = new CommunityContributor();
                    $contributorObj->FirstName = $contributor['first_name'];
                    $contributorObj->LastName = $contributor['last_name'];
                    $contributorObj->Email = $email;

                    if ($email) {
                        $member = $memberRepository->findByEmail($email);
                    }

                    if (!$member && $contributor['first_name'] && $contributor['last_name']) {
                        list($res, $count) = $memberRepository->getAllByName($contributor['first_name'], $contributor['last_name']);
                        if ($count == 1) {
                            $member = $res[0];
                        }
                    }

                    if ($member) {
                        $contributorObj->MemberID = $member->ID;
                    }

                    $contributorObj->write();
                }

                // AWARDS

                //$contributorObj->Awards()->removeAll();

                if ($contributor['awards']) {
                    $awardArray = explode(';', $contributor['awards']);

                    foreach ($awardArray as $award) {
                        //echo 'processing award ' . $award . PHP_EOL;

                        preg_match('#\[(.*?)\]#', $award, $output);

                        // summit
                        $summitArray = explode(' ', $output[1]);
                        $summit = Summit::get()->where("Title LIKE '%{$summitArray[0]}%' AND YEAR(SummitBeginDate) = {$summitArray[1]}")->first();
                        if (!$summit) {
                            echo 'Summit not found for award: '.$award;
                            continue;
                        }

                        // award
                        $award = trim(str_replace($output[0], '', $award));

                        $awardObj = CommunityAward::get()->filter(['Name' => $award, 'SummitID' => $summit->ID])->first();
                        if (!$awardObj) {
                            $awardObj = new CommunityAward();
                            $awardObj->Name = $award;
                            $awardObj->SummitID = $summit->ID;
                            $awardObj->write();
                        }

                        if (!$contributorObj->hasAward($awardObj)) {
                            $contributorObj->Awards()->add($awardObj);
                        }
                    }
                }

                $contributorObj->write();

            }
		}
		catch(Exception $ex){
			SS_Log::log($ex,SS_Log::ERR);
			echo $ex->getMessage();
		}

        return 'OK';
	}
} 