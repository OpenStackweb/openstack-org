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
     * @var IMemberRepository
     */
    private $member_repository;

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
     * @param IMemberRepository $member_repository
     * @param ITransactionManager $tx_manager
     */
    public function __construct(
        ISummitEventRepository $summitevent_repository,
        ISummitPresentationRepository $summitpresentation_repository,
        IEventFeedbackRepository $eventfeedback_repository,
        IEventFeedbackFactory $eventfeedback_factory,
        ISummitAttendeeRepository $attendee_repository,
        IRSVPRepository $rsvp_repository,
        IMemberRepository $member_repository,
        ITransactionManager $tx_manager
    )
    {
        $this->summitevent_repository        = $summitevent_repository;
        $this->summitpresentation_repository = $summitpresentation_repository;
        $this->eventfeedback_repository      = $eventfeedback_repository;
        $this->eventfeedback_factory         = $eventfeedback_factory;
        $this->attendee_repository           = $attendee_repository;
        $this->rsvp_repository               = $rsvp_repository;
        $this->member_repository             = $member_repository;
        $this->tx_manager                    = $tx_manager;
    }


    /**
     * @param int $member_id
     * @param int $event_id
     * @return IAttendeeMember
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

            $member = $this->member_repository->getById($member_id);

            if (!$member) {
                throw new NotFoundEntityException('Member', sprintf('id %s', $event_id));
            }

            if ($member->isOnMySchedule($event_id)) {
                throw new EntityValidationException('Event already exist on member schedule');
            }

            if($event->hasRSVPTemplate()){
                throw new EntityValidationException('Event has RSVP set on it, will be automatically added once you sign in for RSVP.');
            }

            $member->addToSchedule($event);

            return $member;
        });
    }

    /**
     * @param int $member_id
     * @param int $event_id
     * @return IAttendeeMember
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
            $member = $this->member_repository->getById($member_id);

            if (!$member) {
                throw new NotFoundEntityException('Member', sprintf('id %s', $event_id));
            }
            if (!$member->isOnMySchedule($event_id)) {
                throw new NotFoundEntityException('Event does not belong to member', sprintf('id %s', $event_id));
            }

            $member->removeFromSchedule($event);

            return $member;
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
            $member    = $this->member_repository->getById($member_id);

            if (!$member) {
                throw new NotFoundEntityException('Member', '');
            }

            $feedback = $this->eventfeedback_repository->getFeedback($data['event_id'], $member_id);

            if ($feedback)
                throw new EntityValidationException("Feedback already exists for the given member and event.");

            $feedback = $this->eventfeedback_factory->buildEventFeedback($data);
            $this->eventfeedback_repository->add($feedback);

            return $feedback;
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
            $event_id  = intval($data['event_id']);
            $seat_type = $data['seat_type'];

            if (empty($seat_type))
                throw new EntityValidationException("invalid seat type!");

            $event  = $this->summitevent_repository->getById($event_id);
            $member = $this->member_repository->getById($member_id);

            if (is_null($member)) {
                throw new NotFoundEntityException('Member', '');
            }

            if (!$event) {
                throw new NotFoundEntityException('Event', '');
            }

            if (!$event->RSVPTemplate()) {
                throw new EntityValidationException('RSVPTemplate not set');
            }

            // add to schedule the RSVP event
            if (!$member->isOnMySchedule($event_id)) {
                $member->addToSchedule($event);
            }

            $old_rsvp = $this->rsvp_repository->getByEventAndMember($event_id, $member->getIdentifier());
            if (!is_null($old_rsvp))
                throw new EntityValidationException
                (
                    sprintf
                    (
                        "member %s already submitted an rsvp for event %s on summit %s",
                        $member->getIdentifier(),
                        $event_id,
                        $summit_id
                    )
                );

            $rsvp                 = new RSVP();
            $rsvp->EventID        = $event_id;
            $rsvp->SubmittedByID  = $member->getIdentifier();
            $rsvp->SeatType       = $event->getCurrentRSVPSubmissionSeatType();

            if (!$event->couldAddSeatType($rsvp->SeatType))
                throw new EntityValidationException("This event is now full and we are no longer adding to the waitlist.");

            $this->createRSVP($event, $rsvp, $data);

            $rsvp->write();

            if (!is_null($sender_service)) {
                $rsvp->BeenEmailed = true;
                $sender_service->send(['Event' => $event, 'Member' => $member, 'RsvpID' => $rsvp->ID]);
            }

            return $rsvp;
        });
    }

    /**
     * @param ISummitEvent $event
     * @param IRSVP $rsvp
     * @param array $data
     */
    private function createRSVP(ISummitEvent $event, IRSVP $rsvp , array $data){
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
    }

    /**
     * @param array $data
     * @param IMessageSenderService $sender_service
     * @return RSVP
     */
    public function updateRSVP(array $data, IMessageSenderService $sender_service)
    {

        return $this->tx_manager->transaction(function () use (
            $data,
            $sender_service
        ) {

            $member_id = intval($data['member_id']);
            $summit_id = intval($data['summit_id']);
            $event_id  = intval($data['event_id']);
            $rsvp_id   = intval($data['rsvp_id']);
            $seat_type = $data['seat_type'];

            if (empty($seat_type))
                throw new EntityValidationException("invalid seat type!");

            $event  = $this->summitevent_repository->getById($event_id);
            $member = $this->member_repository->getByMemberAndSummit($member_id);

            if (is_null($member)) {
                throw new NotFoundEntityException('Member', '');
            }

            if (!$event) {
                throw new NotFoundEntityException('Event', '');
            }

            if (!$event->RSVPTemplate()) {
                throw new EntityValidationException('RSVPTemplate not set');
            }

            // add to schedule the RSVP event
            if (!$member->isOnMySchedule($event_id)) {
                $member->addToSchedule($event);
            }

            $current_rsvp = $this->rsvp_repository->getByEventAndMember($event_id, $member->getIdentifier());
            $rsvp = $this->rsvp_repository->getById($rsvp_id);

            if (!$rsvp || $current_rsvp->ID != $rsvp_id) {
                throw new EntityValidationException(sprintf("RSVP %s does not correspond to your user.", $rsvp_id));
            }

            $this->createRSVP($event, $rsvp, $data);
            $rsvp->write();

            if (!is_null($sender_service)) {
                $rsvp->BeenEmailed = true;
                $sender_service->send(['Event' => $event, 'Member' => $member, 'RsvpID' => $rsvp->ID]);
            }

            return $rsvp;
        });
    }

    /**
     * @param array $data
     * @return bool
     */
    public function deleteRSVP(array $data)
    {

        return $this->tx_manager->transaction(function () use ($data)
        {

            $member_id = intval($data['member_id']);
            $summit_id = intval($data['summit_id']);
            $event_id  = intval($data['event_id']);

            $member = $this->member_repository->getById($member_id);

            if (is_null($member)) {
                throw new NotFoundEntityException('Member');
            }

            $rsvp = $this->rsvp_repository->getByEventAndMember($event_id, $member->getIdentifier());

            if (!$rsvp || $member->ID != $rsvp->SubmittedBy()->ID || $rsvp->Event()->ID != $event_id) {
                throw new EntityValidationException("RSVP does not correspond to your user.");
            }

            $this->rsvp_repository->delete($rsvp);
            $this->removeEventFromSchedule($member_id, $event_id);

            return true;
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

    /**
     * @param int $member_id
     * @param int $event_id
     * @return bool
     */
    public function addEventToFavorites($member_id, $event_id)
    {
        return $this->tx_manager->transaction(function () use ($member_id, $event_id) {

            $event = $this->summitevent_repository->getById($event_id);
            if (!$event) {
                throw new NotFoundEntityException('Event', sprintf('id %s', $event_id));
            }

            if (!self::allowToSee($event))
                throw new NotFoundEntityException('Event', sprintf('id %s', $event_id));

            $member = $this->member_repository->getById($member_id);

            if (!$member) {
                throw new NotFoundEntityException('Attendee', sprintf('id %s', $event_id));
            }

            if ($member->isOnFavorites($event_id)) {
                throw new EntityValidationException('Event already exist on member favorites');
            }

            $member->addToFavorites($event);

            return true;
        });
    }

    /**
     * @param int $member_id
     * @param int $event_id
     * @return mixed
     */
    public function removeEventFromFavorites($member_id, $event_id)
    {
        return $this->tx_manager->transaction(function () use (
            $member_id,
            $event_id
        ) {

            $event = $this->summitevent_repository->getById($event_id);
            if (!$event) {
                throw new NotFoundEntityException('Event', sprintf('id %s', $event_id));
            }

            $member = $this->member_repository->getById($member_id);

            if (!$member) {
                throw new NotFoundEntityException('Member', sprintf('id %s', $member_id));
            }
            if (!$member->isOnFavorites($event_id)) {
                throw new NotFoundEntityException('Event does not belong to member favorites', sprintf('id %s', $event_id));
            }

            $member->removeFromFavorites($event);

            return true;
        });
    }
}