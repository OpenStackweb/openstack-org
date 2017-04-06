<?php

/**
 * Copyright 2016 OpenStack Foundation
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
final class SpeakerSecondBreakoutAnnouncementSenderManager implements ISpeakerSecondBreakoutAnnouncementSenderManager
{
    const TaskName = 'SpeakerSecondBreakoutEmailSenderTask';

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
     * @var ISpeakerSecondBreakOutSenderFactory
     */
    private $sender_breakout_factory;

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
        ISpeakerSecondBreakOutSenderFactory $sender_breakout_factory,
        ISpeakerSummitRegistrationPromoCodeRepository $promo_code_repository,
        ITransactionManager  $tx_manager
    )
    {

        $this->batch_repository        = $batch_repository;
        $this->batch_task_factory      = $batch_task_factory;
        $this->speaker_repository      = $speaker_repository;
        $this->tx_manager              = $tx_manager;
        $this->sender_factory          = $sender_factory;
        $this->sender_breakout_factory = $sender_breakout_factory;
        $this->promo_code_repository   = $promo_code_repository;
    }

    /**
     * @param ISummit $current_summit
     * @param int $batch_size
     * @return void
     */
    public function send(ISummit $current_summit, $batch_size)
    {
        $speaker_repository     = $this->speaker_repository;
        $sender_factory         = $this->sender_breakout_factory;
        $promo_code_repository  = $this->promo_code_repository;
        $batch_repository       = $this->batch_repository;
        $batch_task_factory     = $this->batch_task_factory;

        $not_allowed_categories = [
            "sponsored sessions",
            "intensive training",
            "ceph day",
            "open vswitch day",
            "birds of a feather",
            "working groups"
        ];

        return $this->tx_manager->transaction(function() use
        (
            $current_summit,
            $batch_size,
            $speaker_repository,
            $sender_factory,
            $promo_code_repository,
            $batch_repository,
            $batch_task_factory,
            $not_allowed_categories
        )
        {
            $summit_id = $current_summit->getIdentifier();

            try {
                $page      = 1;
                $page_size = $batch_size;
                $task      = $batch_repository->findByName(self::TaskName.$summit_id);

                if (is_null($task)) {
                    //create task
                    $task = $batch_task_factory->buildBatchTask(self::TaskName.$summit_id, 0, $page);
                    $batch_repository->add($task);
                }

                $page = $task->getCurrentPage();
                echo "Processing Page " . $page . PHP_EOL;

                list($page, $page_size, $count, $speakers) = $speaker_repository->searchBySummitSchedulePaginated
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

                    if ($speaker->breakoutEmailAlreadySent($current_summit->ID)) continue;

                    /**
                     * Rules are:
                     * All speakers that are in a Track, besides BoF and Working Groups
                     * Send the code they already received, unless they are new and don’t have a code. Then they get a new one.
                     * Send the custom registration link to say they’re coming to the summit and leave their onsite phone
                     * If the user is already registered, we shouldn't send their code again:
                     * they still need the email, just not the part with the code. Probably a slightly altered verbiage as well
                     */
                    $code           = null;
                    $presentations  = $speaker->AllPublishedPresentations($current_summit->getIdentifier());

                    if(intval($presentations->Count()) === 1){
                        $track = strtolower($presentations->first()->Category()->Title);

                        if(in_array($track, $not_allowed_categories))
                        {
                            echo sprintf("skipping speaker %s (%s) - track %s", $speaker->getName(), $speaker->getIdentifier(), $track).PHP_EOL;
                            continue;
                        }
                    }

                    $sender_service = $sender_factory->build($current_summit, $speaker);

                    if (is_null($sender_service)) continue;

                    $params = array
                    (
                        'Speaker' => $speaker,
                        'Summit'  => $current_summit
                    );

                    if(!$speaker->hasConfirmedAssistanceFor($current_summit->getIdentifier()))
                    {
                        echo sprintf("speaker %s (%s) has not confirmed assistance for summit.", $speaker->getName(), $speaker->getIdentifier()).PHP_EOL;
                        if (!$speaker->hasSummitPromoCode($current_summit->getIdentifier())) {
                            echo sprintf("speaker %s (%s) has not promo code for summit.", $speaker->getName(), $speaker->getIdentifier()).PHP_EOL;
                            $code = $promo_code_repository->getNextAvailableByType
                            (
                                $current_summit,
                                ISpeakerSummitRegistrationPromoCode::TypeAccepted,
                                $batch_size
                            );
                            if (is_null($code)) throw new Exception('not available promo code!!!');
                            $speaker->registerSummitPromoCode($code);
                            echo sprintf("speaker %s (%s) has been assigned to promo code %s.", $speaker->getName(), $speaker->getIdentifier(), $code->getCode()).PHP_EOL;
                            $code->write();
                        }
                        $params['PromoCode'] = $speaker->getSummitPromoCode($current_summit->getIdentifier());
                    }
                    echo sprintf("sending to speaker %s (%s) - %s", $speaker->getName(), $speaker->getIdentifier(), $speaker->getEmail()).PHP_EOL;
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
                echo $ex->getMessage().PHP_EOL;
                throw $ex;
            }
        });
    }
}