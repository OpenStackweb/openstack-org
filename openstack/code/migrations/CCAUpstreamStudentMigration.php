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
 * Class CCAUpstreamStudentMigration
 */

final class CCAUpstreamStudentMigration extends AbstractDBMigrationTask {

	protected $title = "CCA and Student Migration";

	protected $description = "Populate CCA and Upstream Institute members";

    function doUp()
    {
        global $database;

        $memberRepository = new SapphireMemberRepository();
        $studentPath = Director::baseFolder() . '/openstack/code/migrations/data/students.yml';
        $ccaPath = Director::baseFolder() . '/openstack/code/migrations/data/cca.yml';


        try {
            // UPSTREAM STUDENT

            $studentYaml = Spyc::YAMLLoadString(file_get_contents($studentPath));

            if( !is_null($studentYaml) ) {
                foreach ($studentYaml as $student) {

                    //echo 'processing student ' . $student['last_name'] . PHP_EOL;

                    if ($email = $student['email']) {
                        $email = rtrim($email, ", ");
                        $studentObj = OSUpstreamInstituteStudent::get()->filter('Email', $email)->first();
                    }

                    if (!$studentObj && $student['first_name'] && $student['last_name']) {
                        $studentObj = OSUpstreamInstituteStudent::get()
                            ->filter(['FirstName' => $student['first_name'], 'LastName' => $student['last_name']])->first();
                    }

                    if (!$studentObj) {
                        $studentObj = new OSUpstreamInstituteStudent();
                        $studentObj->FirstName = $student['first_name'];
                        $studentObj->LastName = $student['last_name'];
                        $studentObj->Email = $email;

                        if ($email) {
                            $member = $memberRepository->findByEmail($email);
                        }

                        if (!$member && $student['first_name'] && $student['last_name']) {
                            list($res, $count) = $memberRepository->getAllByName($student['first_name'], $student['last_name']);
                            if ($count == 1) {
                                $member = $res[0];
                            }
                        }

                        if ($member) {
                            $studentObj->MemberID = $member->ID;
                        }

                        $studentObj->write();
                    }
                }
            }

            // CCA

            $ccaYaml = Spyc::YAMLLoadString(file_get_contents($ccaPath));

            if( !is_null($ccaYaml) ) {
                foreach ($ccaYaml as $contributor) {

                    //echo 'processing contributor ' . $contributor['last_name'] . PHP_EOL;

                    if ($email = $contributor['email_']) {
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

                    $contributorObj->Awards()->removeAll();

                    if ($contributor['awards']) {
                        $awardArray = explode(',', $contributor['awards']);

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


        } catch (Exception $ex) {
            echo $ex->getMessage().PHP_EOL;
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return;
        }


    }

    function doDown()
    {

    }
}