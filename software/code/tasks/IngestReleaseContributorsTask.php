<?php
/**
 * Copyright 2018 OpenStack Foundation
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

use Symfony\Component\Yaml\Yaml;

/**
 * Class IngestReleaseContributorsTask
 */
final class IngestReleaseContributorsTask extends CronTask
{
    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * IngestReleaseContributorsTask constructor.
     * @param ITransactionManager $tx_manager
     */
    public function __construct(ITransactionManager $tx_manager)
    {
        parent::__construct();
        $this->tx_manager = $tx_manager;
    }

    /**
     * @return void
     */
    public function run()
    {
        $start = time();
        $member_repository = new SapphireMemberRepository();

        $this->tx_manager->transaction(function () use ($member_repository) {

            $folder = DataObject::get_one("Folder", "Filename = 'assets/atcs/'");
            $files = DataObject::get("File", "ParentID = '{$folder->ID}'");

            foreach ($files as $file) {
                $content = file_get_contents(BASE_PATH .'/'. $file->Filename);
                $releaseName = ucfirst(explode('-', $file->Name)[0]);

                $release = OpenStackRelease::get()->filter('Name', $releaseName)->first();
                $entries = Yaml::parse($content);

                if (!$release) { continue; }

                echo 'Parsing '. $file->Name . ' - Release: '. $release->Name . PHP_EOL;

                foreach($entries as $entryId => $contributor) {
                    if (!$contributor || count($contributor) == 0) continue;

                    $email = (isset($contributor['preferred'])) ? $contributor['preferred'] : '';
                    $name = explode(' ', $contributor['name']);
                    $firstName = array_shift($name);
                    $lastName = implode(' ', $name);

                    $contributorObj = ReleaseCycleContributor::get()->filter(['Email' => $email, 'ReleaseID' => $release->ID])->first();
                    if (!$contributorObj) {
                        $contributorObj = new ReleaseCycleContributor();
                    }
                    $contributorObj->FirstName = $firstName;
                    $contributorObj->LastName = $lastName;

                    $contributorObj->LastCommit = $contributor['newest'];
                    $contributorObj->FirstCommit = $contributor['oldest'];
                    $contributorObj->Email = $contributor['preferred'];
                    $contributorObj->IRCHandle = $contributor['username'];
                    $contributorObj->CommitCount = $contributor['count'];
                    if (count($contributor['extra'])) {
                        $contributorObj->ExtraEmails = implode(',', $contributor['extra']);
                    }

                    $memberFound = false;
                    if (isset($contributor['member']) && $contributor['member']) {
                        if($member = Member::get()->byID($contributor['member'])) {
                            $contributorObj->MemberID = $member->ID;
                            $memberFound = true;
                        }
                    }

                    // find by irc
                    if (!$memberFound && $contributor['username'] != '_non_code_contributor') {
                        $member = Member::get()->filter('IRCHandle', $contributor['username'])->first();
                        if ($member) {
                            $contributorObj->MemberID = $member->ID;
                            $memberFound = true;
                        }
                    }

                    // find by email
                    if (!$memberFound) {
                        $emails = array_merge([$email], $contributor['extra']);
                        foreach ($emails as $an_email) {
                            $member = $member_repository->findByEmail($an_email);
                            if ($member) {
                                $contributorObj->MemberID = $member->ID;
                                break;
                            }
                        }
                    }

                    $contributorObj->ReleaseID = $release->ID;
                    $contributorObj->write();
                }
            }
        });

        $delta = time() - $start;
        echo sprintf('task took %s seconds to run.',$delta).PHP_EOL;
    }
}