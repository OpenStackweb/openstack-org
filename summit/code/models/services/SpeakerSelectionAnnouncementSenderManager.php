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
final class SpeakerSelectionAnnouncementSenderManager implements ISpeakerSelectionAnnouncementSenderManager
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
     * @var ISpeakerRepository
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

        $this->batch_repository         = $batch_repository;
        $this->batch_task_factory      = $batch_task_factory;
        $this->speaker_repository      = $speaker_repository;
        $this->tx_manager              = $tx_manager;
        $this->sender_factory          = $sender_factory;
        $this->promo_code_repository   = $promo_code_repository;
    }

    public function send(ISummit $current_summit, $batch_size){

        $speaker_repository    = $this->speaker_repository;
        $sender_factory        = $this->sender_factory;
        $promo_code_repository = $this->promo_code_repository;
        $batch_repository      = $this->batch_repository;
        $batch_task_factory    = $this->batch_task_factory;

        return $this->tx_manager->transaction(function() use
        (
            $current_summit,
            $batch_size,
            $speaker_repository,
            $sender_factory,
            $promo_code_repository,
            $batch_repository,
            $batch_task_factory
        )
        {
            try {

                $page      = 1;
                $page_size = $batch_size;
                $task      = $batch_repository->findByName(self::TaskName.'_'.$current_summit->getIdentifier());

                if (is_null($task)) {
                    //create task
                    $task = $batch_task_factory->buildBatchTask(self::TaskName.'_'.$current_summit->getIdentifier(), 0, $page);
                    $batch_repository->add($task);
                }

                $page = $task->getCurrentPage();
                echo "Processing Page " . $page . PHP_EOL;
                // get speakers with not email sent for this current summit

                list($page, $page_size, $count, $speakers) = $speaker_repository->searchBySummitPaginated
                (
                    $current_summit,
                    $page,
                    $page_size
                );

                $speakers_notified = 0;

                foreach ($speakers as $speaker) {

                    if (!$speaker instanceof IPresentationSpeaker) continue;
                    // we need an email for this speaker ...
                    $email = $speaker->getEmail();
                    if (empty($email)) continue;

                    if ($speaker->announcementEmailAlreadySent($current_summit->ID)) continue;

                    $sender_service = $sender_factory->build($current_summit, $speaker);
                    // get registration code
                    if (is_null($sender_service)) continue;

                    $code = null;

                    if ($speaker->hasPublishedPresentations($current_summit->getIdentifier())) //get approved code
                    {
                        $code = $promo_code_repository->getNextAvailableByType
                        (
                            $current_summit,
                            ISpeakerSummitRegistrationPromoCode::TypeAccepted,
                            $batch_size
                        );
                        if (is_null($code)) throw new Exception('not available promo code!!!');
                    }
                    else if ($speaker->hasAlternatePresentations($current_summit->getIdentifier())) // get alternate code
                    {
                        $code = $promo_code_repository->getNextAvailableByType
                        (
                            $current_summit,
                            ISpeakerSummitRegistrationPromoCode::TypeAlternate,
                            $batch_size
                        );
                        if (is_null($code)) throw new Exception('not available alternate promo code!!!');
                    }

                    $params = array
                    (
                        'Speaker' => $speaker,
                        'Summit'  => $current_summit
                    );

                    if (!is_null($code)) {
                        $speaker->registerSummitPromoCode($code);
                        $code->setEmailSent(true);
                        $code->write();
                        $params['PromoCode'] = $code;
                    }

                    $sender_service->send($params);
                    ++$speakers_notified;
                }
                $task->updatePage($count, $page_size);
                $task->write();
                return $speakers_notified;
            }
            catch(Exception $ex)
            {
                SS_Log::log($ex->getMessage(), SS_Log::ERR);
                throw $ex;
            }
        });
    }

}