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
    private $attendee_repository;

    /**
     * @var IEntityRepository
     */
    private $rsvp_repository;

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * ScheduleManager constructor.
     * @param IEntityRepository $summitevent_repository
     * @param IEntityRepository $summitpresentation_repository
     * @param IEntityRepository $eventfeedback_repository
     * @param IEventFeedbackFactory $eventfeedback_factory
     * @param IEntityRepository $attendee_repository
     * @param ITransactionManager $tx_manager
     */
    public function __construct(
        IEntityRepository $summitevent_repository,
        IEntityRepository $summitpresentation_repository,
        IEntityRepository $eventfeedback_repository,
        IEventFeedbackFactory $eventfeedback_factory,
        IEntityRepository $attendee_repository,
        IEntityRepository $rsvp_repository,
        ITransactionManager $tx_manager
    ) {
        $this->summitevent_repository = $summitevent_repository;
        $this->summitpresentation_repository = $summitpresentation_repository;
        $this->eventfeedback_repository = $eventfeedback_repository;
        $this->eventfeedback_factory = $eventfeedback_factory;
        $this->attendee_repository = $attendee_repository;
        $this->rsvp_repository = $rsvp_repository;
        $this->tx_manager = $tx_manager;
    }


    /**
     * @param $member_id
     * @param $event_id
     * @return mixed
     */
    public function addEventToSchedule($member_id, $event_id)
    {

        $this_var               = $this;
        $summitevent_repository = $this->summitevent_repository;
        $attendee_repository    = $this->attendee_repository;

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

            $attendee = $attendee_repository->getByMemberAndSummit($member_id, $event->Summit()->getIdentifier());

            if (!$attendee) {
                throw new NotFoundEntityException('Attendee', sprintf('id %s', $event_id));
            }
            if($attendee->isScheduled($event_id)){
                throw new EntityValidationException('Event already exist on attendee schedule');
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
            if(!$attendee->isScheduled($event_id)){
                throw new NotFoundEntityException('Event does not belong to attendee', sprintf('id %s', $event_id));
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
        $eventfeedback_repository      = $this->eventfeedback_repository;
        $eventfeedback_factory         = $this->eventfeedback_factory;
        $attendee_repository           = $this->attendee_repository;

        return $this->tx_manager->transaction(function () use (
            $data,
            $attendee_repository,
            $eventfeedback_repository,
            $eventfeedback_factory
        ) {

            $member_id = intval($data['member_id']);
            $summit_id = intval($data['summit_id']);
            $attendee  = $attendee_repository->getByMemberAndSummit($member_id, $summit_id);

            if (!$attendee) {
                throw new NotFoundEntityException('Attendee', '');
            }

            $feedback = $eventfeedback_repository->getFeedback($data['event_id'],$member_id);
            if ($feedback) {
                $feedback->Rate = $data['rating'];
                $feedback->Note = $data['comment'];
                $feedback->write();
            } else {
                $feedback  = $eventfeedback_factory->buildEventFeedback($data);
                $eventfeedback_repository->add($feedback);
            }

            return $feedback;
        });
    }

    /**
     * @param $member_id
     * @param $event_id
     * @return mixed
     */
    public function saveSynchId($member_id, $event_id, $target, $cal_event_id)
    {

        $this_var               = $this;
        $summitevent_repository = $this->summitevent_repository;
        $attendee_repository    = $this->attendee_repository;

        return $this->tx_manager->transaction(function () use (
            $this_var,
            $member_id,
            $event_id,
            $target,
            $cal_event_id,
            $attendee_repository,
            $summitevent_repository
        ) {

            $event = $summitevent_repository->getById($event_id);
            if (!$event) {
                throw new NotFoundEntityException('Event', sprintf('id %s', $event_id));
            }

            $attendee = $attendee_repository->getByMemberAndSummit($member_id, $event->Summit()->getIdentifier());

            if (!$attendee) {
                throw new NotFoundEntityException('Attendee', sprintf('id %s', $event_id));
            }

            if ($target == 'google')
                $attendee->setGoogleCalEventId($event, $cal_event_id);

            return $cal_event_id;
        });
    }

    /**
     * @param $data
     * @return mixed
     */
    public function addRSVP($data)
    {
        $rsvp_repository        = $this->rsvp_repository;
        $attendee_repository    = $this->attendee_repository;
        $summitevent_repository = $this->summitevent_repository;

        return $this->tx_manager->transaction(function () use (
            $data,
            $attendee_repository,
            $rsvp_repository,
            $summitevent_repository
        ) {

            $member_id = intval($data['member_id']);
            $summit_id = intval($data['summit_id']);
            $event_id  = intval($data['event_id']);
            $event     = $summitevent_repository->getById($event_id);

            if (!$event) {
                throw new NotFoundEntityException('Event', '');
            }

            if (!$event->RSVPTemplate()) {
                throw new EntityValidationException('RSVPTemplate not set');
            }

            $rsvp = $rsvp_repository->getByEventAndSubmitter($event_id, $member_id);

            if (!$rsvp) {
                $rsvp = new RSVP();
                $rsvp->EventID       = $event_id;
                $rsvp->SubmittedByID = $member_id;
            }

            foreach ($event->RSVPTemplate()->getQuestions() as $q)
            {
                $question_name = $q->name();

                if ($q instanceof RSVPDropDownQuestionTemplate) {
                    $question_name = ($q->IsMultiSelect) ? $q->name().'[]' : $question_name;
                } else if ($q instanceof RSVPCheckBoxListQuestionTemplate) {
                    $question_name = $q->name().'[]';
                }

                if (isset($data[$question_name]))
                {
                    if (!$rsvp || !$answer = $rsvp->findAnswerByQuestion($q)) {
                        $answer = new RSVPAnswer();
                    }

                    $answer_value = $data[$question_name];

                    if(is_array($answer_value) ){
                        $answer_value = str_replace('{comma}', ',', $answer_value);
                        $answer->Value = implode(',', $answer_value);
                    }
                    else{
                        $answer->Value = $answer_value;
                    }
                    $answer->QuestionID = $q->getIdentifier();
                    $answer->write();

                    $rsvp->addAnswer($answer);
                }
            }

            $rsvp->write();

            return $rsvp;
        });
    }
} 