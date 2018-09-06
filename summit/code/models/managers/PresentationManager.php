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

/**
 * Class PresentationManager
 */
final class PresentationManager implements IPresentationManager
{
    /**
     * @var ISummitRepository
     */
    private $summit_repository;

    /**
     * @var ISummitEventRepository
     */
    private $event_repository;

    /**
     * @var ISpeakerRepository
     */
    private $speaker_repository;

    /**
     * @var IMemberRepository
     */
    private $member_repository;

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * @var ISummitPresentationRepository
     */
    private $presentation_repository;

    /**
     * @var ISpeakerRegistrationRequestManager
     */
    private $speaker_registration_manager;

    /**
     * PresentationManager constructor.
     * @param ISummitRepository $summit_repository
     * @param ISummitEventRepository $event_repository
     * @param ISummitPresentationRepository $presentation_repository
     * @param ISpeakerRepository $speaker_repository
     * @param IMemberRepository $member_repository
     * @param ISpeakerRegistrationRequestManager $speaker_registration_manager
     * @param ITransactionManager $tx_manager
     */
    public function __construct
    (
        ISummitRepository $summit_repository,
        ISummitEventRepository $event_repository,
        ISummitPresentationRepository $presentation_repository,
        ISpeakerRepository $speaker_repository,
        IMemberRepository $member_repository,
        ISpeakerRegistrationRequestManager $speaker_registration_manager,
        ITransactionManager $tx_manager
    )
    {
        $this->summit_repository            = $summit_repository;
        $this->event_repository             = $event_repository;
        $this->presentation_repository      = $presentation_repository;
        $this->speaker_repository           = $speaker_repository;
        $this->member_repository            = $member_repository;
        $this->speaker_registration_manager = $speaker_registration_manager;
        $this->tx_manager                   = $tx_manager;
    }

    /**
     * @param PresentationSpeaker $speaker
     * @param ISummit $summit
     * @return bool
     */
    public function isPresentationSubmissionAllowedFor(PresentationSpeaker $speaker, ISummit $summit)
    {
        if($summit->isCallForSpeakersOpen())
        {
            $selectionPlan = $summit->getOpenSelectionPlanForStage('Submission');
            $max_per_summit = intval($selectionPlan->getMaxSubmissions());

            $presentation_count = intval($speaker->getPresentationsByPlan($selectionPlan)->count());

            return $presentation_count < $max_per_summit;
        }

        return false;
    }

    /**
     * @param PresentationSpeaker $speaker
     * @param Presentation $presentation
     * @return bool
     */
    public function canAddSpeakerOnPresentation(PresentationSpeaker $speaker, Presentation $presentation)
    {
        $category = $presentation->Category();
        $summit   = $category->Summit();

        if($summit->isCallForSpeakersOpen())
        {
            $selectionPlan = $summit->getOpenSelectionPlanForStage('Submission');
            $max_per_summit     = intval($selectionPlan->getMaxSubmissions());

            $presentation_count = intval($speaker->getPresentationsByPlan($selectionPlan)->count());

            return $presentation_count < $max_per_summit;
        }

        return false;
    }

    /**
     * @throws EntityValidationException
     * @return int
     */
    public function getSubmissionLimitFor(ISummit $summit)
    {
        $res           = -1;
        if($summit->isCallForSpeakersOpen()) {
            $res = intval($summit->getOpenSelectionPlanForStage('Submission')->getMaxSubmissions());
        } else {
            throw new EntityValidationException("Call for speaker is closed!");
        }

        // zero means infinity
        $res = $res === 0 ? PHP_INT_MAX : $res;
        return $res;
    }

    /**
     * @param ISummit $summit
     * @param PresentationSpeaker $speaker
     * @return bool
     */
    public function isCallForSpeakerOpen(ISummit $summit, PresentationSpeaker $speaker)
    {
        if(!$summit->Active) return false;
        $res = $summit->isCallForSpeakersOpen();

        return $res;
    }

    /**
     * @param $presentation_id
     * @param PresentationSpeaker $speaker
     * @return bool
     */
    public function canEditPresentation($presentation_id, PresentationSpeaker $speaker){

        $presentation = Presentation::get()->byID($presentation_id);
        if(is_null($presentation) || !$presentation->canEdit()) return false;

        $summit = $presentation->Summit();
        if(!$summit->Active) return false;
        $selection_plan = $presentation->SelectionPlan();

        if($selection_plan && $selection_plan->isCallForPresentationsOpen()) return true;

        return false;
    }

    /**
     * @param Member $member
     * @param ISummit $summit
     * @return bool
     */
    public function isPresentationEditionAllowed(Member $member, ISummit $summit)
    {
        return $summit->isPresentationEditionAllowed();
    }

    /**
     * @param ISummit $summit
     * @param Member $creator
     * @param array $data
     * @return IPresentation
     */
    public function registerPresentationOn(ISummit $summit, Member $creator, array $data)
    {
        return $this->tx_manager->transaction(function() use($summit, $creator, $data){

            if(!$summit->isCallForSpeakersOpen()) {
                throw new EntityValidationException('Call for Presentations is closed!');
            }

            $selectionPlan  = $summit->getOpenSelectionPlanForStage('Submission');
            $speaker        = $creator->getSpeakerProfile();

            $limit    = intval($selectionPlan->getMaxSubmissions());
            $count    = intval($speaker->getPresentationsByPlan($selectionPlan)->count());

            if ($count >= $limit)
                throw new EntityValidationException(sprintf('You reached the limit (%s) of presentations.', $limit));


            $presentation                          = Presentation::create();
            $presentation->Title                   = trim($data['Title']);
            $presentation->TypeID                  = intval($data['TypeID']);
            $presentation->Level                   = trim($data['Level']);
            $presentation->Abstract                = trim($data['Abstract']);
            $presentation->SocialSummary           = trim($data['SocialSummary']);
            $presentation->AttendeesExpectedLearnt = trim($data['AttendeesExpectedLearnt']);
            $presentation->CategoryID              = intval(trim($data['CategoryID']));
            $presentation->AttendingMedia          = isset($data['AttendingMedia']) ? $data['AttendingMedia'] : 0;

            if(isset($data['OtherTopic']))
                $presentation->OtherTopic = trim($data['OtherTopic']);

            $presentation->SummitID         = $summit->getIdentifier();
            $presentation->SelectionPlanID  = $selectionPlan->ID;
            $presentation->CreatorID        = $creator->ID;
            $presentation->Progress         = Presentation::PHASE_SUMMARY;

            $presentation->write();

            if(isset($data["PresentationLink"]))
            {
                foreach($data["PresentationLink"] as $id => $val)
                {
                    if(empty($val)) continue;
                    $presentation->Materials()->add(PresentationLink::create(array('Name' => trim($val), 'Link' => trim($val))));
                }
            }

            $extra_questions = ($presentation->Category()->Exists()) ? $presentation->Category()->ExtraQuestions() : array();
            foreach ($extra_questions as $question) {
                if(!isset($data[$question->Name])) continue;
                $answer_value = $data[$question->Name];
                if(empty($answer_value)) continue;

                if (!$answer = $presentation->findAnswerByQuestion($question)) {
                    $answer = new TrackAnswer();
                }

                if(is_array($answer_value) ){
                    $answer_value = str_replace('{comma}', ',', $answer_value);
                    $answer->Value = implode(',', $answer_value);
                }
                else{
                    $answer->Value = $answer_value;
                }
                $answer->QuestionID = $question->getIdentifier();
                $answer->write();

                $presentation->ExtraAnswers()->add($answer);
            }

            return $presentation;
        });
    }

    /**
     * @param IPresentation $presentation
     * @param array $data
     * @return IPresentation
     */
    public function updatePresentationSummary(IPresentation $presentation, array $data)
    {
        //TODO: this method and registerPresentationOn should be refactored
        return $this->tx_manager->transaction(function() use($presentation,  $data){

            $presentation->Title                   = trim($data['Title']);
            $presentation->TypeID                  = intval($data['TypeID']);
            $presentation->Level                   = trim($data['Level']);
            $presentation->Abstract                = trim($data['Abstract']);
            $presentation->SocialSummary           = trim($data['SocialSummary']);
            $presentation->AttendeesExpectedLearnt = trim($data['AttendeesExpectedLearnt']);
            $presentation->CategoryID              = intval(trim($data['CategoryID']));
            $presentation->AttendingMedia          = isset($data['AttendingMedia']) ? $data['AttendingMedia'] : 0;

            // remove moderator if its not needed
            if (!$presentation->Type()->UseModerator) {
                $presentation->ModeratorID = 0;
            }

            if(isset($data['OtherTopic']))
                $presentation->OtherTopic = trim($data['OtherTopic']);

            $old_materials = $presentation->Materials()->filter('ClassName', 'PresentationLink');
            foreach($old_materials as $o) $o->Delete();

            if(isset($data["PresentationLink"]))
            {
                foreach($data["PresentationLink"] as $id => $val)
                {
                    if(empty($val)) continue;
                    $presentation->Materials()->add(PresentationLink::create(array('Link' => trim($val))));
                }
            }

            $extra_questions = ($presentation->Category()->Exists()) ? $presentation->Category()->ExtraQuestions() : array();
            foreach ($extra_questions as $question) {
                if (!isset($data[$question->Name])) continue;
                if (!$data[$question->Name]) continue;

                $answer_value = $data[$question->Name];
                if(empty($answer_value)) continue;

                if (!$answer = $presentation->findAnswerByQuestion($question)) {
                    $answer = new TrackAnswer();
                }

                if(is_array($answer_value) ){
                    $answer_value = str_replace('{comma}', ',', $answer_value);
                    $answer->Value = implode(',', $answer_value);
                }
                else{
                    $answer->Value = $answer_value;
                }
                $answer->QuestionID = $question->getIdentifier();
                $answer->write();

                $presentation->ExtraAnswers()->add($answer);
            }

            $presentation->write();

            return $presentation;
        });
    }

    /**
     * @param int $presentation_id
     * @return void
     */
    public function removePresentation($presentation_id)
    {
       $this->tx_manager->transaction(function() use($presentation_id){

           $presentation = $this->presentation_repository->getById($presentation_id);
           if(is_null($presentation)) throw new NotFoundEntityException(sprintf('presentation id %s', $presentation_id));

           if (!$presentation->canDelete())
               throw new EntityValidationException('you cant delete this presentation!');

           $this->presentation_repository->delete($presentation);
       });
    }

    /**
     * @param IPresentation $presentation
     * @param Member $member
     * @param $vote
     */
    public function voteFor(IPresentation $presentation, Member $member, $vote){
        $this->tx_manager->transaction(function() use($presentation, $member, $vote){
            $presentation->setUserVote($vote);
            $member->removePresentation($presentation->getIdentifier());
        });
    }

    /**
     * @param IPresentation $presentation
     * @param string $email
     * @param Member|null $member
     * @param IPresentationSpeaker|null $speaker
     * @return IPresentationSpeaker
     */
    public function addSpeakerByEmailTo(IPresentation $presentation, $email, Member $member = null, IPresentationSpeaker $speaker = null)
    {

        return $this->tx_manager->transaction(function() use($presentation, $email, $member, $speaker){

            $speaker = !is_null($speaker)? $speaker : $this->speaker_repository->getByEmail($email);

            if(is_null($speaker) && !is_null($member))
                $speaker = $member->getSpeakerProfile();

            if(!is_null($speaker) && !is_null($member) && intval($member->ID) !== intval($speaker->MemberID))
                throw new EntityValidationException(sprintf('speaker does not belongs to selected member!'));

            if (!$speaker) {
                // create it
                $speaker = PresentationSpeaker::create();
                $speaker->write();

                if(!is_null($member)) {
                    $speaker->MemberID = $member->ID;
                    $member->addToGroupByCode('speakers');
                    $member->write();
                }
                else
                {
                    $speaker->MemberID              = 0;
                    $request                        = $this->speaker_registration_manager->register($speaker, $email);
                    $speaker->RegistrationRequestID = $request->getIdentifier();
                }
                $speaker->write();
            }


            // i am adding other speaker than me
            if(!is_null($member)  &&
                intval($member->ID) !== intval(Member::currentUserID()) &&
                !$this->canAddSpeakerOnPresentation($speaker, $presentation))
            {
                throw new EntityValidationException
                (
                    sprintf
                    (
                        "You reached the max. allowed # of presentations for speaker %s (%s)",
                        $speaker->getName(),
                        $email
                    )
                );
            }

            if($speaker->Presentations()->filter('PresentationID', $presentation->ID)->count() > 0
                || $presentation->ModeratorID == $speaker->ID)
                throw new EntityValidationException('Speaker already assigned to this presentation!.');

            // The first one is the moderator.
            if (!$presentation->maxModeratorsReached()) {
                $presentation->ModeratorID = $speaker->ID;
            } else {
                $speaker->Presentations()->add($presentation);
                $speaker->write();
            }

            return $speaker;
        });

    }

    /**
     * @param IPresentation $presentation
     * @param IMessageSenderService $speakers_message_sender
     * @param IMessageSenderService $creator_message_sender
     * @param IMessageSenderService $moderator_message_sender
     * @return IPresentation
     */
    public function completePresentation
    (
        IPresentation $presentation,
        IMessageSenderService $speakers_message_sender,
        IMessageSenderService $creator_message_sender,
        IMessageSenderService $moderator_message_sender
    )
    {
        return $this->tx_manager->transaction(function() use
        (
            $presentation,
            $speakers_message_sender,
            $creator_message_sender,
            $moderator_message_sender
        )
        {

            $speakers = $presentation->Speakers()->exclude(array(
                'MemberID' => $presentation->CreatorID
            ));

            $presentation->markReceived()->write();

            foreach ($speakers as $speaker)
            {
                 $speakers_message_sender->send(['Presentation' => $presentation, 'Speaker' => $speaker]);
            }

            $creator_message_sender->send(['Presentation'=> $presentation]);

            if($presentation->Moderator()->exists()){
                $moderator_message_sender->send(['Presentation' => $presentation]);
            }

            return $presentation;
        });
    }

    /**
     * @param IPresentation $presentation
     * @param IPresentationSpeaker $speaker
     * @return void
     */
    public function removeSpeakerFrom(IPresentation $presentation, IPresentationSpeaker $speaker)
    {
        return $this->tx_manager->transaction(function() use
        (
            $presentation,
            $speaker
        )
        {

            if (!$presentation->canRemoveSpeakers())
                throw new EntityValidationException('You cannot remove speakers from this presentation');

            if($presentation->isModerator($speaker)) {
                $presentation->unsetModerator();
                return;
            }

            $presentation->removeSpeaker($speaker);

        });
    }
}