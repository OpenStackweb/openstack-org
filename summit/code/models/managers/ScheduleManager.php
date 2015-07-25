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

/**
 * Class ScheduleManager
 */
final class ScheduleManager
{

    /**
     * @var IEntityRepository
     */
    private $summitevent_repository;

    /**
     * @var IEntityRepository
     */
    private $summitpresentation_repository;

    /**
     * @var IEntityRepository
     */
    private $eventfeedback_repository;

    /**
     * @var IEventFeedbackFactory
     */
    private $eventfeedback_factory;

    /**
     * @var IEntityRepository
     */
    private $speakerfeedback_repository;

    /**
     * @var IEntityRepository
     */
    private $attendee_repository;

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * @param IEntityRepository $summitevent_repository
     * @param IEntityRepository $attendee_repository
     * @param ITransactionManager $tx_manager
     */
    public function __construct(
        IEntityRepository $summitevent_repository,
        IEntityRepository $summitpresentation_repository,
        IEntityRepository $eventfeedback_repository,
        IEventFeedbackFactory $eventfeedback_factory,
        IEntityRepository $speakerfeedback_repository,
        IEntityRepository $attendee_repository,
        ITransactionManager $tx_manager
    ) {
        $this->summitevent_repository = $summitevent_repository;
        $this->summitpresentation_repository = $summitpresentation_repository;
        $this->eventfeedback_repository = $eventfeedback_repository;
        $this->eventfeedback_factory = $eventfeedback_factory;
        $this->speakerfeedback_repository = $speakerfeedback_repository;
        $this->attendee_repository = $attendee_repository;
        $this->tx_manager = $tx_manager;
    }


    /**
     * @param $member_id
     * @param $event_id
     * @return mixed
     */
    public function addEventToSchedule($member_id, $event_id)
    {

        $this_var = $this;
        $summitevent_repository = $this->summitevent_repository;
        $attendee_repository = $this->attendee_repository;

        return $this->tx_manager->transaction(function () use (
            $this_var,
            $member_id,
            $event_id,
            $attendee_repository,
            $summitevent_repository
        ) {

            $event = $summitevent_repository->getById($event_id);
            if (!$event) {
                throw new NotFoundEntityException('Event', sprintf('id %s', $event_id));
            }

            $attendee = $attendee_repository->getByMemberAndSummit($member_id, $event->Summit->getIdentifier());

            if (!$attendee) {
                throw new NotFoundEntityException('Attendee', sprintf('id %s', $event_id));
            }

            $attendee->addToSchedule($event);
            PublisherSubscriberManager::getInstance()->publish(ISummitEntityEvent::AddedToSchedule,
                array($member_id, $event));

            return $attendee;
        });
    }

    /**
     * @param $member_id
     * @param $event_id
     * @return mixed
     */
    public function removeEventFromSchedule($member_id, $event_id)
    {

        $this_var = $this;
        $summitevent_repository = $this->summitevent_repository;
        $attendee_repository = $this->attendee_repository;

        return $this->tx_manager->transaction(function () use (
            $this_var,
            $member_id,
            $event_id,
            $attendee_repository,
            $summitevent_repository
        ) {

            $event = $summitevent_repository->getById($event_id);
            if (!$event) {
                throw new NotFoundEntityException('Event', sprintf('id %s', $event_id));
            }
            $attendee = $attendee_repository->getByMemberAndSummit($member_id, $event->Summit->getIdentifier());

            if (!$attendee) {
                throw new NotFoundEntityException('Attendee', sprintf('id %s', $event_id));
            }

            $attendee->removeFromSchedule($event);
            PublisherSubscriberManager::getInstance()->publish(ISummitEntityEvent::RemovedToSchedule,
                array($member_id, $event));

            return $attendee;
        });
    }

    /**
     * @param array $data
     * @param ISummitEventFeedback $feedback
     * @return ISummitEventFeedback
     */
    public function updateFeedback(array $data, ISummitEventFeedback $feedback){
        $this_var = $this;
        $summitpresentation_repository = $this->summitpresentation_repository;
        $eventfeedback_repository = $this->eventfeedback_repository;
        $speakerfeedback_repository = $this->speakerfeedback_repository;
        $eventfeedback_factory = $this->eventfeedback_factory;

        return $this->tx_manager->transaction(function () use (
            $this_var,
            $data,
            $feedback,
            $summitpresentation_repository,
            $eventfeedback_repository,
            $eventfeedback_factory,
            $speakerfeedback_repository
        ) {
            if(!$feedback)
                throw new NotFoundEntityException('SummitEventFeedback',sprintf('id %s',$feedback->getIdentifier()));

            $feedback_temp = $eventfeedback_factory->buildEventFeedback($data);
            $feedback->Note = $feedback_temp->Note;
            $feedback->Rate = $feedback_temp->Rate;

            $presentation = $summitpresentation_repository->getById($data['event_id']);
            if ($presentation) {
                foreach ($presentation->getSpeakers()->toArray() as $speaker) {
                    $speaker_feedback = $speakerfeedback_repository->getFeedback($speaker->getIdentifier(),$data['event_id'],$data['member_id']);
                    $speaker_feedback->Note = $feedback_temp->Note;
                    $speaker_feedback->Rate = $feedback_temp->Rate;
                }
            }

            return $feedback;
        });
    }

    /**
     * @param $data
     * @return mixed
     */
    public function addFeedback($data)
    {
        $this_var = $this;
        $summitpresentation_repository = $this->summitpresentation_repository;
        $eventfeedback_repository = $this->eventfeedback_repository;
        $speakerfeedback_repository = $this->speakerfeedback_repository;
        $eventfeedback_factory = $this->eventfeedback_factory;

        return $this->tx_manager->transaction(function () use (
            $this_var,
            $data,
            $summitpresentation_repository,
            $eventfeedback_repository,
            $eventfeedback_factory,
            $speakerfeedback_repository
        ) {

            $feedback = $eventfeedback_factory->buildEventFeedback($data);
            $feedback_id = $eventfeedback_repository->add($feedback);

            $presentation = $summitpresentation_repository->getById($data['event_id']);
            if ($presentation) {
                foreach ($presentation->getSpeakers()->toArray() as $speaker) {
                    $speaker_feedback = $eventfeedback_factory->buildSpeakerFeedback($data,$speaker);
                    $speakerfeedback_repository->add($speaker_feedback);
                }
            }

            return $feedback_id;
        });
    }

} 