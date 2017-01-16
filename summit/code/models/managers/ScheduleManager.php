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
final class ScheduleManager implements IScheduleManager
{

    /**
     * @var ISummitEventRepository
     */
    private $summitevent_repository;

    /**
     * @var ISummitPresentationRepository
     */
    private $summitpresentation_repository;

    /**
     * @var IEventFeedbackRepository
     */
    private $eventfeedback_repository;

    /**
     * @var IEventFeedbackFactory
     */
    private $eventfeedback_factory;

    /**
     * @var ISummitAttendeeRepository
     */
    private $attendee_repository;

    /**
     * @var IRSVPRepository
     */
    private $rsvp_repository;

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * ScheduleManager constructor.
     * @param ISummitEventRepository $summitevent_repository
     * @param ISummitPresentationRepository $summitpresentation_repository
     * @param IEventFeedbackRepository $eventfeedback_repository
     * @param IEventFeedbackFactory $eventfeedback_factory
     * @param ISummitAttendeeRepository $attendee_repository
     * @param IRSVPRepository $rsvp_repository
     * @param ITransactionManager $tx_manager
     */
    public function __construct(
        ISummitEventRepository $summitevent_repository,
        ISummitPresentationRepository $summitpresentation_repository,
        IEventFeedbackRepository $eventfeedback_repository,
        IEventFeedbackFactory $eventfeedback_factory,
        ISummitAttendeeRepository $attendee_repository,
        IRSVPRepository $rsvp_repository,
        ITransactionManager $tx_manager
    )
    {
        $this->summitevent_repository = $summitevent_repository;
        $this->summitpresentation_repository = $summitpresentation_repository;
        $this->eventfeedback_repository = $eventfeedback_repository;
        $this->eventfeedback_factory = $eventfeedback_factory;
        $this->attendee_repository = $attendee_repository;
        $this->rsvp_repository = $rsvp_repository;
        $this->tx_manager = $tx_manager;
    }


    /**
     * @param int $member_id
     * @param int $event_id
     * @return ISummitAttendee
     */
    public function addEventToSchedule($member_id, $event_id)
    {

        return $this->tx_manager->transaction(function () use ($member_id, $event_id) {

            $event = $this->summitevent_repository->getById($event_id);
            if (!$event) {
                throw new NotFoundEntityException('Event', sprintf('id %s', $event_id));
            }

            if (!self::allowToSee($event))
                throw new NotFoundEntityException('Event', sprintf('id %s', $event_id));

            $attendee = $this->attendee_repository->getByMemberAndSummit($member_id, $event->Summit()->getIdentifier());

            if (!$attendee) {
                throw new NotFoundEntityException('Attendee', sprintf('id %s', $event_id));
            }
            if ($attendee->isScheduled($event_id)) {
                throw new EntityValidationException('Event already exist on attendee schedule');
            }

            $attendee->addToSchedule($event);
            PublisherSubscriberManager::getInstance()->publish(ISummitEntityEvent::AddedToSchedule,
                array($member_id, $event));

            return $attendee;
        });
    }


    /**
     * @param int $member_id
     * @param int $event_id
     * @return ISummitAttendee
     */
    public function removeEventFromSchedule($member_id, $event_id)
    {

        return $this->tx_manager->transaction(function () use (
            $member_id,
            $event_id
        ) {

            $event = $this->summitevent_repository->getById($event_id);
            if (!$event) {
                throw new NotFoundEntityException('Event', sprintf('id %s', $event_id));
            }
            $attendee = $this->attendee_repository->getByMemberAndSummit($member_id, $event->Summit->getIdentifier());

            if (!$attendee) {
                throw new NotFoundEntityException('Attendee', sprintf('id %s', $event_id));
            }
            if (!$attendee->isScheduled($event_id)) {
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
    public function updateFeedback(array $data, ISummitEventFeedback $feedback)
    {

        return $this->tx_manager->transaction(function () use (
            $data,
            $feedback
        ) {
            if (!$feedback)
                throw new NotFoundEntityException('SummitEventFeedback', sprintf('id %s', $feedback->getIdentifier()));

            $feedback_temp = $this->eventfeedback_factory->buildEventFeedback($data);
            $feedback->Note = $feedback_temp->Note;
            $feedback->Rate = $feedback_temp->Rate;

            $presentation = $this->summitpresentation_repository->getById($data['event_id']);
            if ($presentation) {
                foreach ($presentation->getSpeakers()->toArray() as $speaker) {
                    $speaker_feedback = $this->speakerfeedback_repository->getFeedback($speaker->getIdentifier(), $data['event_id'], $data['member_id']);
                    $speaker_feedback->Note = $feedback_temp->Note;
                    $speaker_feedback->Rate = $feedback_temp->Rate;
                }
            }

            return $feedback;
        });
    }

    /**
     * @param array $data
     * @return ISummitEventFeedBack
     */
    public function addFeedback(array $data)
    {

        return $this->tx_manager->transaction(function () use ($data) {

            $member_id = intval($data['member_id']);
            $summit_id = intval($data['summit_id']);
            $attendee = $this->attendee_repository->getByMemberAndSummit($member_id, $summit_id);

            if (!$attendee) {
                throw new NotFoundEntityException('Attendee', '');
            }

            $feedback = $this->eventfeedback_repository->getFeedback($data['event_id'], $member_id);
            if ($feedback) {
                $feedback->Rate = $data['rating'];
                $feedback->Note = $data['comment'];
                $feedback->write();
            } else {
                $feedback = $this->eventfeedback_factory->buildEventFeedback($data);
                $this->eventfeedback_repository->add($feedback);
            }

            return $feedback;
        });
    }

    /**
     * @param int $member_id
     * @param int $event_id
     * @param string $target
     * @param int $cal_event_id
     * @return int
     */
    public function saveSynchId($member_id, $event_id, $target = 'google', $cal_event_id)
    {

        return $this->tx_manager->transaction(function () use (
            $member_id,
            $event_id,
            $target,
            $cal_event_id
        ) {

            $event = $this->summitevent_repository->getById($event_id);
            if (!$event) {
                throw new NotFoundEntityException('Event', sprintf('id %s', $event_id));
            }

            $attendee = $this->attendee_repository->getByMemberAndSummit($member_id, $event->Summit()->getIdentifier());

            if (!$attendee) {
                throw new NotFoundEntityException('Attendee', sprintf('id %s', $event_id));
            }

            if ($target == 'google')
                $attendee->setGoogleCalEventId($event, $cal_event_id);

            return $cal_event_id;
        });
    }

    /**
     * @param array $data
     * @param IMessageSenderService $sender_service
     * @return RSVP
     */
    public function addRSVP(array $data, IMessageSenderService $sender_service)
    {

        return $this->tx_manager->transaction(function () use (
            $data,
            $sender_service
        ) {

            $member_id = intval($data['member_id']);
            $summit_id = intval($data['summit_id']);
            $event_id = intval($data['event_id']);
            $seat_type = $data['seat_type'];

            if (empty($seat_type))
                throw new EntityValidationException("invalid seat type!");

            $event = $this->summitevent_repository->getById($event_id);
            $summit_attendee = $this->attendee_repository->getByMemberAndSummit(intval($member_id), intval($summit_id));

            if (is_null($summit_attendee)) {
                throw new EntityValidationException(sprintf("there is no any attendee for member %s and summit %s", $member_id, $summit_id));
            }

            if (!$event) {
                throw new NotFoundEntityException('Event', '');
            }

            if (!$event->RSVPTemplate()) {
                throw new EntityValidationException('RSVPTemplate not set');
            }

            // add to schedule the RSVP event
            if (!$summit_attendee->isScheduled($event_id)) {
                $summit_attendee->addToSchedule($event);
            }

            $old_rsvp = $this->rsvp_repository->getByEventAndAttendee($event_id, $summit_attendee->getIdentifier());
            if (!is_null($old_rsvp))
                throw new EntityValidationException
                (
                    sprintf
                    (
                        "attendee %s already submitted an rsvp for event %s on summit %s",
                        $summit_attendee->getIdentifier(),
                        $event_id,
                        $summit_id
                    )
                );

            $rsvp = new RSVP();
            $rsvp->EventID = $event_id;
            $rsvp->SubmittedByID = $summit_attendee->getIdentifier();
            $rsvp->SeatType = $event->getCurrentRSVPSubmissionSeatType();

            if (!$event->couldAddSeatType($rsvp->SeatType))
                throw new EntityValidationException
                (
                    sprintf
                    (
                        "you reach the limit of rsvp items for current event %s ( regular seats %s - wait list seats %s)",
                        $event_id,
                        $event->getCurrentSeatsCountByType(IRSVP::SeatTypeRegular),
                        $event->getCurrentSeatsCountByType(IRSVP::SeatTypeWaitList)
                    )
                );

            foreach ($event->RSVPTemplate()->getQuestions() as $q) {
                $question_name = $q->name();

                if ($q instanceof RSVPDropDownQuestionTemplate) {
                    $question_name = ($q->IsMultiSelect) ? $q->name() . '[]' : $question_name;
                } else if ($q instanceof RSVPCheckBoxListQuestionTemplate) {
                    $question_name = $q->name() . '[]';
                }

                if (isset($data[$question_name])) {
                    if (!$rsvp || !$answer = $rsvp->findAnswerByQuestion($q)) {
                        $answer = new RSVPAnswer();
                    }

                    $answer_value = $data[$question_name];

                    if (is_array($answer_value)) {
                        $answer_value = str_replace('{comma}', ',', $answer_value);
                        $answer->Value = implode(',', $answer_value);
                    } else {
                        $answer->Value = $answer_value;
                    }
                    $answer->QuestionID = $q->getIdentifier();
                    $answer->write();

                    $rsvp->addAnswer($answer);
                }
            }

            if (!is_null($sender_service)) {
                $rsvp->BeenEmailed = true;
                $sender_service->send(['Event' => $event, 'Attendee' => $summit_attendee]);
            }

            $rsvp->write();

            return $rsvp;
        });
    }

    /**
     * @param ISummitEvent $summit_event
     * @return bool
     */
    static public function allowToSee(ISummitEvent $summit_event)
    {

        if (SummitEventType::isPrivate($summit_event->getType()->Type)) {
            if (!Member::currentUserID())
                return false;

            if (Member::currentUser()->isAdmin()) return true;

            // i am logged, check if i have permissions
            if ($summit_event instanceof SummitGroupEvent) {

                $member_groups_code = [];
                $event_groups_code = [];

                foreach (Member::currentUser()->Groups() as $member_group) {
                    $member_groups_code[] = $member_group->Code;
                }

                foreach ($summit_event->Groups() as $event_group) {
                    $event_groups_code[] = $event_group->Code;
                }

                return count(array_intersect($event_groups_code, $member_groups_code)) > 0;
            }
            return true;
        }
        return true;
    }
}