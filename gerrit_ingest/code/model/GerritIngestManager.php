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
     * @var IBatchTaskRepository
     */
    private $batch_repository;

    /**
     * @var ICLAMemberRepository
     */
    private $member_repository;

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * @var IBatchTaskFactory
     */
    private $batch_task_factory;

    const PullCommitsFromGerritTask = 'PullCommitsFromGerritTask';

    /**
     * @param IGerritAPI           $gerrit_api
     * @param IBatchTaskRepository $batch_repository
     * @param ICLAMemberRepository $member_repository
     * @param IBatchTaskFactory    $batch_task_factory
     * @param ITransactionManager  $tx_manager
     */
    public function __construct(IGerritAPI $gerrit_api,
                                IBatchTaskRepository $batch_repository,
                                ICLAMemberRepository $member_repository,
                                IBatchTaskFactory    $batch_task_factory,
                                ITransactionManager  $tx_manager){

        $this->gerrit_api         = $gerrit_api;
        $this->batch_repository   = $batch_repository;
        $this->member_repository  = $member_repository;
        $this->batch_task_factory = $batch_task_factory;
        $this->tx_manager         = $tx_manager;
    }


    public function processCommits($batch_size){
        $batch_repository   = $this->batch_repository;
        $member_repository  = $this->member_repository;
        $gerrit_api         = $this->gerrit_api;
        $batch_task_factory = $this->batch_task_factory;

        return $this->tx_manager->transaction(function() use($batch_size, $member_repository, $batch_repository, $gerrit_api, $batch_task_factory) {

            $task = $batch_repository->findByName(GerritIngestManager::PullCommitsFromGerritTask);

            $last_index      = 0;
            $members         = array();
            $updated_members = 0;

            if($task){
                $last_index = $task->lastRecordProcessed();
                list($members, $total_size) = $member_repository->getAllICLAMembers($last_index, $batch_size);
                if($task->lastRecordProcessed() >= $total_size) $task->initialize($total_size);
            }
            else{
                list($members,$total_size) = $member_repository->getAllICLAMembers($last_index, $batch_size);
                $task = $batch_task_factory->buildBatchTask(GerritIngestManager::PullCommitsFromGerritTask, $total_size);
                $batch_repository->add($task);
            }

            foreach($members as $member){
                $more_changes = false;
                $start_point  = null;
                do {
                    $changes = $gerrit_api->getUserCommits($member->getGerritId(), GerritChangeStatus::_MERGED, 250, $start_point);
                    if (!is_null($changes) && is_array($changes) && count($changes) > 0) {
                        $count = count($changes);
                        $last  = $changes[$count - 1];
                        $more_changes = isset($last['_more_changes'])?$last['_more_changes']:false;
                        $start_point = ($more_changes)?$last['_sortkey']:null;

                        foreach($changes as $change){
                            $db_change = GerritChangeInfo::get()->filter(array('ChangeId' => $change['change_id']))->first();
                            if(!$db_change){
                                $db_change = new GerritChangeInfo();
                                $db_change->kind = @$change['kind'];
                                $db_change->FormattedChangeId = @$change['id'];
                                $db_change->ProjectName = @$change['project'];
                                $db_change->Branch = @$change['branch'];
                                $db_change->Topic = @$change['topic'];
                                $db_change->ChangeId = @$change['change_id'];
                                $db_change->Subject = @$change['subject'];
                                $db_change->Status = @$change['status'];
                                $created_date = explode('.',@$change['created']);
                                $updated_date = explode('.',@$change['updated']);
                                $db_change->CreatedDate = DateTime::createFromFormat('Y-m-d H:i:s', $created_date[0])->getTimestamp();
                                $db_change->UpdatedDate = DateTime::createFromFormat('Y-m-d H:i:s', $updated_date[0])->getTimestamp();
                                $db_change->MemberID  = $member->getIdentifier();
                                $db_change->write();
                            }
                        }
                    }
                }while($more_changes);
                ++$updated_members;
                $task->updateLastRecord();
            }
            return $updated_members;
        });
    }
}