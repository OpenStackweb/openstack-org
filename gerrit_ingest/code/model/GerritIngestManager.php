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
 * Class GerritIngestManager
 */
final class GerritIngestManager {

    /**
     * @var IGerritAPI
     */
    private $gerrit_api;

    /**
     * @var IGerritUserRepository
     */
    private $member_repository;

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * GerritIngestManager constructor.
     * @param IGerritAPI $gerrit_api
     * @param IGerritUserRepository $member_repository
     * @param ITransactionManager $tx_manager
     */
    public function __construct(IGerritAPI $gerrit_api,
                                IGerritUserRepository $member_repository,
                                ITransactionManager  $tx_manager){

        $this->gerrit_api         = $gerrit_api;
        $this->member_repository  = $member_repository;
        $this->tx_manager         = $tx_manager;
    }

    const CommitsPageSize  = 250;

    public function processCommits(){


        $member_repository               = $this->member_repository;
        $gerrit_api                      = $this->gerrit_api;
        list($total_size, $gerrit_users) = $member_repository->getAllGerritUsersByPage(1, PHP_INT_MAX);
        $pages                           = array_chunk($gerrit_users, self::CommitsPageSize);
        $processed                       = 0;
        echo sprintf("** we found %s gerrit user pages to process ...", count($pages)) . PHP_EOL;
        $page_nbr = 1;
        foreach($pages as $gerrit_user_page) {
            echo "*********************************************************". PHP_EOL;
            echo sprintf("** processing page nbr %s  ...", $page_nbr) . PHP_EOL;
            echo "*********************************************************". PHP_EOL;
            ++$page_nbr;
            $processed += $this->tx_manager->transaction(function () use ($member_repository, $gerrit_api, $gerrit_user_page) {

                $updated_members = 0;

                foreach ($gerrit_user_page as $gerrit_user) {
                    $more_changes = false;
                    $initial_commits_count = $gerrit_user->Commits()->count();
                    $start = $initial_commits_count > 0 ? $initial_commits_count : 0;
                    echo sprintf("gerrit user %s (%s) , initial commits %s .", $gerrit_user->Email, $gerrit_user->AccountID, $initial_commits_count) . PHP_EOL;
                    $page_counter = 0;

                    do {

                        echo sprintf("processing start %s for gerrit user %s (%s)", $start, $gerrit_user->Email, $gerrit_user->AccountID) . PHP_EOL;
                        $changes = $gerrit_api->getUserCommits($gerrit_user->AccountID, GerritChangeStatus::_MERGED, self::CommitsPageSize, $start);
                        if (!is_null($changes) && is_array($changes) && count($changes) > 0) {
                            $count = count($changes);
                            $last = $changes[$count - 1];
                            echo sprintf("gerrit user %s (%s), has commits %s to process.", $gerrit_user->Email, $gerrit_user->AccountID, $count) . PHP_EOL;
                            /*
                             * If the n query parameter is supplied and additional changes exist that match the query beyond
                             * the end, the last change object has a _more_changes: true JSON field set.
                             * The S or start query parameter can be supplied to skip a number of changes from the list.
                             */
                            $more_changes = isset($last['_more_changes']) ? $last['_more_changes'] : false;
                            ++$page_counter;

                            if ($more_changes) {
                                $start += $count;
                                echo sprintf("gerrit user %s (%s), has more pending commits to process , new start %s.", $gerrit_user->Email, $gerrit_user->AccountID, $start) . PHP_EOL;
                            }

                            foreach ($changes as $change) {
                                $db_change = GerritChangeInfo::get()->filter(array('ChangeId' => $change['change_id']))->first();
                                if (!$db_change) {
                                    $db_change = new GerritChangeInfo();
                                    $db_change->kind = @$change['kind'];
                                    $db_change->FormattedChangeId = @$change['id'];
                                    $db_change->ProjectName = @$change['project'];
                                    $db_change->Branch = @$change['branch'];
                                    $db_change->Topic = @$change['topic'];
                                    $db_change->ChangeId = @$change['change_id'];
                                    $db_change->Subject = @$change['subject'];
                                    $db_change->Status = @$change['status'];
                                    $created_date = explode('.', @$change['created']);
                                    $updated_date = explode('.', @$change['updated']);
                                    $db_change->CreatedDate = DateTime::createFromFormat('Y-m-d H:i:s', $created_date[0])->getTimestamp();
                                    $db_change->UpdatedDate = DateTime::createFromFormat('Y-m-d H:i:s', $updated_date[0])->getTimestamp();
                                    $db_change->OwnerID = $gerrit_user->ID;

                                    $db_change->write();
                                }
                            }
                        }
                        else{
                            echo sprintf("** gerrit user %s (%s) does not has available commits this time!", $gerrit_user->Email, $gerrit_user->AccountID) . PHP_EOL;
                        }
                    } while ($more_changes);
                    ++$updated_members;
                }
                return $updated_members;
            });
        }
        return $processed;
    }
}