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
final class SpeakerEmailAnnouncementSenderManager
    implements ISpeakerEmailAnnouncementSenderManager
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

    /**
     * SpeakerEmailAnnouncementSenderManager constructor.
     * @param IBatchTaskRepository $batch_repository
     * @param IBatchTaskFactory $batch_task_factory
     * @param IEntityRepository $speaker_repository
     * @param ISpeakerSelectionAnnouncementSenderFactory $sender_factory
     * @param ISpeakerSummitRegistrationPromoCodeRepository $promo_code_repository
     * @param ITransactionManager $tx_manager
     */
    public function __construct
    (
        IBatchTaskRepository $batch_repository,
        IBatchTaskFactory $batch_task_factory,
        IEntityRepository $speaker_repository,
        ISpeakerSelectionAnnouncementSenderFactory $sender_factory,
        ISpeakerSummitRegistrationPromoCodeRepository $promo_code_repository,
        ITransactionManager $tx_manager
    )
    {

        $this->batch_repository      = $batch_repository;
        $this->batch_task_factory    = $batch_task_factory;
        $this->speaker_repository    = $speaker_repository;
        $this->tx_manager            = $tx_manager;
        $this->sender_factory        = $sender_factory;
        $this->promo_code_repository = $promo_code_repository;
    }

    /**
     * @param IPresentationSpeaker $speaker
     * @param ISummit $current_summit
     * @param bool|string $role
     * @param bool $check_email_existance
     * @return bool
     */
    public function sendSelectionAnnouncementEmailForSpeaker(IPresentationSpeaker $speaker, ISummit $current_summit, $role = IPresentationSpeaker::RoleSpeaker, $check_email_existance = true){

        return $this->tx_manager->transaction(function () use (
            $current_summit,
            $speaker,
            $role,
            $check_email_existance
        ) {
            // we need an email for this speaker ...
            $email = $speaker->getEmail();
            if (empty($email)) return false;

            if ($check_email_existance && $speaker->announcementEmailAlreadySent($current_summit->ID)) return false;

            $sender_service = $this->sender_factory->build($current_summit, $speaker, $role);

            // get registration code
            if (is_null($sender_service)) {
                echo sprintf('excluding email to %s', $email) . PHP_EOL;
                return false;
            }

            $code = null;

            $has_published = $speaker->hasPublishedRegularPresentations($current_summit->getIdentifier(), $role, true, $current_summit->getExcludedTracksForPublishedPresentations()) ||
                             $speaker->hasPublishedLightningPresentations($current_summit->getIdentifier(), $role, true, $current_summit->getExcludedTracksForPublishedPresentations());
            $has_alternate = $speaker->hasAlternatePresentations($current_summit->getIdentifier(), $role, true, $current_summit->getExcludedTracksForAlternatePresentations());
            if(!$speaker->hasSummitPromoCode($current_summit->getIdentifier())) {
                if ($has_published) //get approved code
                {
                    $code = $this->promo_code_repository->getNextAvailableByType
                    (
                        $current_summit,
                        ISpeakerSummitRegistrationPromoCode::TypeAccepted
                    );
                    if (is_null($code)) throw new Exception('not available promo code!!!');
                } else if ($has_alternate) // get alternate code
                {
                    $code = $this->promo_code_repository->getNextAvailableByType
                    (
                        $current_summit,
                        ISpeakerSummitRegistrationPromoCode::TypeAlternate
                    );
                    if (is_null($code)) throw new Exception('not available alternate promo code!!!');
                }
            }
            else{
                $code = $speaker->getSummitPromoCode($current_summit->getIdentifier());
            }

            $params = [
                'Speaker'            => $speaker,
                'Summit'             => $current_summit,
                'Role'               => $role,
                'CheckMailExistance' => $check_email_existance
            ];

            if (!is_null($code)) {
                $speaker->registerSummitPromoCode($code);
                $code->setEmailSent(true);
                $code->write();
                $params['PromoCode'] = $code;
            }

            echo sprintf('sending email to %s', $email) . PHP_EOL;
            $sender_service->send($params);

            return true;
        });
    }

    public function sendSpeakersSelectionAnnouncementBySummit(ISummit $current_summit, $batch_size)
    {
       return $this->tx_manager->transaction(function () use (
            $current_summit,
            $batch_size
        ) {
            try {

                $page      = 1;
                $page_size = $batch_size;
                $task      = $this->batch_repository->findByName(self::TaskName . '_' . $current_summit->getIdentifier());

                if (is_null($task)) {
                    //create task
                    $task = $this->batch_task_factory->buildBatchTask(self::TaskName . '_' . $current_summit->getIdentifier(), 0, $page);
                    $this->batch_repository->add($task);
                }

                $page = $task->getCurrentPage();
                echo "Processing Page " . $page . PHP_EOL;
                // get speakers with not email sent for this current summit

                list($page, $page_size, $count, $speakers) = $this->speaker_repository->searchNonModeratorsBySummitPaginated
                (
                    $current_summit,
                    $page,
                    $page_size
                );

                $speakers_notified = 0;

                echo sprintf('total speakers %s - count %s', $count, count($speakers)).PHP_EOL;

                foreach ($speakers as $speaker) {

                    if($this->sendSelectionAnnouncementEmailForSpeaker($speaker, $current_summit))
                        ++$speakers_notified;
                }
                $task->updatePage($count, $page_size);
                $task->write();
                return $speakers_notified;
            } catch (Exception $ex) {
                SS_Log::log($ex->getMessage(), SS_Log::ERR);
                throw $ex;
            }
        });
    }

    /**
     * @param ISummit $current_summit
     * @param int $batch_size
     * @return void
     */
    public function sendModeratorsSelectionAnnouncementBySummit(ISummit $current_summit, $batch_size)
    {

        return $this->tx_manager->transaction(function () use (
            $current_summit,
            $batch_size
        ) {
            try {

                $page      = 1;
                $page_size = $batch_size;
                $task      = $this->batch_repository->findByName(self::TaskName . '_MODERATORS_' . $current_summit->getIdentifier());

                if (is_null($task)) {
                    //create task
                    $task = $this->batch_task_factory->buildBatchTask(self::TaskName . '_MODERATORS_' . $current_summit->getIdentifier(), 0, $page);
                    $this->batch_repository->add($task);
                }

                $page = $task->getCurrentPage();
                echo "Processing Page " . $page . PHP_EOL;
                // get speakers with not email sent for this current summit

                list($page, $page_size, $count, $moderators) = $this->speaker_repository->searchModeratorsBySummitPaginated
                (
                    $current_summit,
                    $page,
                    $page_size
                );

                $speakers_notified = 0;

                echo sprintf('total speakers %s - count %s', $count, count($moderators)).PHP_EOL;

                foreach ($moderators as $moderator) {
                    if($this->sendSelectionAnnouncementEmailForSpeaker($moderator, $current_summit, IPresentationSpeaker::RoleModerator))
                        ++$speakers_notified;
                }
                $task->updatePage($count, $page_size);
                $task->write();
                return $speakers_notified;
            } catch (Exception $ex) {
                SS_Log::log($ex->getMessage(), SS_Log::ERR);
                throw $ex;
            }
        });
    }


    /**
     * @param ISummit $current_summit
     * @param IMessageSenderService $sender_service
     * @param int $batch_size
     * @return int
     */
    public function sendUploadSlidesAnnouncementBySummit(ISummit $current_summit, IMessageSenderService $sender_service, $batch_size)
    {
        return $this->tx_manager->transaction(function () use ($current_summit, $sender_service, $batch_size) {

            $page      = 1;
            $page_size = $batch_size;
            $task      = $this->batch_repository->findByName(self::TaskName . '_SLIDE_UPLOAD_' . $current_summit->getIdentifier());

            if (is_null($task)) {
                //create task
                $task = $this->batch_task_factory->buildBatchTask(self::TaskName . '_SLIDE_UPLOAD_' . $current_summit->getIdentifier(), 0, $page);
                $this->batch_repository->add($task);
            }

            $page = $task->getCurrentPage();
            echo "Processing Page " . $page . PHP_EOL;

            list($page, $page_size, $count, $speakers) = $this->speaker_repository->searchBySummitSchedulePaginated
            (
                $current_summit,
                $page,
                $page_size
            );

            $send                   = 0;
            echo sprintf('total speakers %s - page count %s', $count, count($speakers)).PHP_EOL;

            foreach ($speakers as $speaker) {
                if(!$speaker instanceof IPresentationSpeaker) continue;

                // we need an email for this speaker ...
                $email = $speaker->getEmail();
                if (empty($email) || !EmailValidator::validEmail($email)){
                    echo sprintf("Skipping %s (%s). Has not valid email", $speaker->getName(), $speaker->ID) . PHP_EOL;
                    continue;
                }

                $presentations  = $speaker->AllPublishedPresentations($current_summit->getIdentifier(), $current_summit->getExcludedTracksForUploadPresentationSlideDeck());

                if($presentations->Count() == 0){
                    echo sprintf("skipping speaker %s (%s) - no published presentations available", $speaker->getName(), $speaker->getEmail()).PHP_EOL;
                    continue;
                }

                if($speaker->hasUploadSlidesRequestEmail($current_summit)){
                    echo sprintf("skipping speaker %s (%s) - email already sent!", $speaker->getName(), $speaker->getEmail()).PHP_EOL;
                    continue;
                }

                $sender_service->send([
                    'Presentations' => $presentations,
                    'Speaker'       => $speaker,
                    'Summit'        => $current_summit,
                ]);

                ++$send;

                echo sprintf('sending email to %s', $email) . PHP_EOL;
            }

            $task->updatePage($count, $page_size);
            $task->write();

            return $send;
        });
    }
}