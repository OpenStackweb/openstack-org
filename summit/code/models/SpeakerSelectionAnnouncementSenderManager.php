<?php

/**
 * Copyright 2015 OpenStack Foundation
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
final class SpeakerSelectionAnnouncementSenderManager
{

    const TaskName = 'SpeakerSelectionAnnouncementSenderTask';

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * @var IBatchTaskFactory
     */
    private $batch_task_factory;

    /**
     * @var IBatchTaskRepository
     */
    private $batch_repository;

    /**
     * @var IEntityRepository
     */
    private $speaker_repository;

    /**
     * @var ISpeakerSelectionAnnouncementSenderFactory
     */
    private $sender_factory;

    /**
     * @var ISpeakerSummitRegistrationPromoCodeRepository
     */
    private $promo_code_repository;

    public function __construct
    (
        IBatchTaskRepository $batch_repository,
        IBatchTaskFactory    $batch_task_factory,
        IEntityRepository    $speaker_repository,
        ISpeakerSelectionAnnouncementSenderFactory $sender_factory,
        ISpeakerSummitRegistrationPromoCodeRepository $promo_code_repository,
        ITransactionManager  $tx_manager
    )
    {

        $this->batch_repository      = $batch_repository;
        $this->batch_task_factory    = $batch_task_factory;
        $this->speaker_repository    = $speaker_repository;
        $this->tx_manager            = $tx_manager;
        $this->sender_factory        = $sender_factory;
        $this->promo_code_repository = $promo_code_repository;
    }


    public function send($batch_size){

        $batch_repository   = $this->batch_repository;
        $batch_task_factory = $this->batch_task_factory;
        $speaker_repository = $this->speaker_repository;
        $sender_factory     = $this->sender_factory;
        $promo_code_repository = $this->promo_code_repository;

        return $this->tx_manager->transaction(function() use($batch_size, $batch_repository, $speaker_repository, $batch_task_factory, $sender_factory, $promo_code_repository) {

            $task = $batch_repository->findByName(self::TaskName);
            $last_index      = 0;
            $speakers        = array();
            $speakers_notified = 0;

            $query      = new QueryObject;
            $query->addAndCondition(QueryCriteria::equal('SummitID', Summit::get_active()->ID));

            if($task)
            {
                $last_index = $task->lastRecordProcessed();
                list($speakers,$total_size) = $speaker_repository->getAll($query, $last_index, $batch_size);
                if($task->lastRecordProcessed() >= $total_size) $task->initialize($total_size);
            }
            else{
                list($speakers,$total_size) = $speaker_repository->getAll($query, $last_index, $batch_size);
                $task = $batch_task_factory->buildBatchTask(self::TaskName, $total_size);
                $batch_repository->add($task);
            }

            foreach($speakers as $speaker)
            {
                if(!$speaker instanceof IPresentationSpeaker) continue;

                if($speaker->announcementEmailAlreadySent()) continue;

                $sender_service = $sender_factory->build($speaker);
                // get registration code
                if(is_null($sender_service)) continue;

                $code          = null;

                if($speaker->hasApprovedPresentations()) //get approved code
                {
                    $code = $promo_code_repository->getNextAvailableByType(ISpeakerSummitRegistrationPromoCode::TypeAccepted);
                    if(is_null($code)) throw new Exception('not available promo code!!!');
                }

                if($speaker->hasAlternatePresentations()) // get alternate code
                {
                    $code = $promo_code_repository->getNextAvailableByType(ISpeakerSummitRegistrationPromoCode::TypeAlternate);
                    if(is_null($code)) throw new Exception('not available promo code!!!');
                }

                if(!is_null($code))
                    $speaker->registerSummitPromoCode($code);

                $sender_service->send($speaker);
                ++$speakers_notified;
                $task->updateLastRecord();
            }

            return $speakers_notified;
        });
    }

}