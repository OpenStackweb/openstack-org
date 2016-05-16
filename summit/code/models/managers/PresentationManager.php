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
     * @param Member $member
     * @param ISummit $summit
     * @return PresentationCategory[]
     */
    public function getAvailableCategoriesFor(Member $member, ISummit $summit)
    {
        $res    = array();
        if(!$summit->Active) return array();

        $private_groups = $summit->getPrivateCategoryGroups();

        if($summit->isCallForSpeakersOpen())
        {
            //check public categories
            foreach($summit->Categories() as $public_category){
                $is_private = false;
                foreach($private_groups as $private_group){
                    if($private_group->Categories()->filter('PresentationCategoryID', $public_category->ID)->count() > 0){
                        $is_private = true;
                        break;
                    }
                }
                if(!$is_private) array_push($res, $public_category);
            }
        }
        // private categories
        $groups = $this->getPrivateCategoryGroupsFor($member, $summit);

        foreach($groups as $g)
        {
            if(!$g->isSubmissionOpen()) continue;
            $res = array_merge($res, $g->Categories()->toArray());
        }

        return $res;
    }

    /**
     * @param PresentationSpeaker $speaker
     * @param ISummit $summit
     * @return bool
     */
    public function isPresentationSubmissionAllowedFor(PresentationSpeaker $speaker, ISummit $summit)
    {
        $res = false;

        if($summit->isCallForSpeakersOpen())
        {
            $max_per_summit = intval($summit->MaxSubmissionAllowedPerUser);
            // zero means infinity
            if ($max_per_summit === 0) $max_per_summit = PHP_INT_MAX;

            $presentation_count = intval($speaker->getPublicCategoryPresentationsBySummit($summit)->count())
                + intval($speaker->getPublicCategoryOwnedPresentationsBySummit($summit)->count());

            $res = $presentation_count < $max_per_summit;
        }

        if($speaker->Member()->exists() && $groups = $this->getPrivateCategoryGroupsFor($speaker->Member(), $summit))
        {
            // check private groups
            foreach($groups as $g)
            {
                if(!$g->isSubmissionOpen()) continue;
                $max_per_group = intval($g->MaxSubmissionAllowedPerUser);
                if($max_per_group === 0) $max_per_group = PHP_INT_MAX; /// infinite
                // we need to check
                $group_presentation_count = intval($speaker->getPrivateCategoryPresentationsBySummit($summit, $g))
                                            + intval($speaker->getPrivateCategoryOwnedPresentationsBySummit($summit, $g));
                $res = $group_presentation_count < $max_per_group;
                if($res) break;
            }
        }
        return $res;
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
        if($summit->isCallForSpeakersOpen() && $summit->isPublicCategory($category))
        {
            $max_per_summit     = intval($summit->MaxSubmissionAllowedPerUser);
            //zero means infinity
            if($max_per_summit === 0) $max_per_summit = PHP_INT_MAX;
            $presentation_count = intval($speaker->getPublicCategoryPresentationsBySummit($summit)->count())
                + intval($speaker->getPublicCategoryOwnedPresentationsBySummit($summit)->count());
            return $presentation_count < $max_per_summit;
        }

        if($speaker->Member()->exists() && $groups = $this->getPrivateCategoryGroupsFor($speaker->Member(), $summit))
        {
            // check private groups
            foreach($groups as $g)
            {
                if(!$g->isSubmissionOpen()) continue;
                $max_per_group = intval($g->MaxSubmissionAllowedPerUser);
                //zero means infinity
                if($max_per_group === 0) $max_per_group = PHP_INT_MAX;
                if(!$g->hasCategory($category)) continue;
                // we need to check
                $group_presentation_count =
                    intval($speaker->getPrivateCategoryPresentationsBySummit($summit, $g))
                    + intval($speaker->getPrivateCategoryOwnedPresentationsBySummit($summit, $g));
                return $group_presentation_count < $max_per_group;
            }
        }

        return false;
    }

    /**
     * @param PresentationSpeaker $speaker
     * @param PresentationCategory $category
     * @return int
     */
    public function getSubmissionLimitFor(PresentationSpeaker $speaker, PresentationCategory $category)
    {
        $summit = $category->Summit();
        $res    = -1;
        if($summit->isCallForSpeakersOpen() && $summit->isPublicCategory($category))
            $res = intval($summit->MaxSubmissionAllowedPerUser);

        if($speaker->Member()->exists() && $groups = $this->getPrivateCategoryGroupsFor($speaker->Member(), $summit)){
            foreach($groups as $g) {
                if (!$g->isSubmissionOpen()) continue;
                if (!$g->hasCategory($category)) continue;
                $res = intval($g->MaxSubmissionAllowedPerUser);
                break;
            }
        }
        // zero means infinity
        $res = $res === 0 ? PHP_INT_MAX : $res;
        return $res;
    }

    /**
     * @param Member $member
     * @param ISummit $summit
     * @return PrivatePresentationCategoryGroup[]
     */
    public function getPrivateCategoryGroupsFor(Member $member, ISummit $summit){

        $groups         = array();
        $private_groups = $summit->getPrivateCategoryGroups();

        foreach($private_groups as $private_group)
        {
            foreach($private_group->AllowedGroups() as $user_group)
            {
                $already_added = false;
                if($member->inGroup($user_group))
                {
                    array_push($groups, $private_group);
                    $already_added = true;
                    break;
                }
                if($already_added) break;
            }
        }
        return $groups;
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

        if(!$res && $speaker->Member()->exists())
        {
            $groups = $this->getPrivateCategoryGroupsFor($speaker->Member(), $summit);
            foreach($groups as $g)
            {
                if(!$g->isSubmissionOpen()) continue;
                if($g->Categories()->count() == 0) continue;
                $res = true;
                break;
            }
        }
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
        $category = $presentation->Category();

        if($summit->isCallForSpeakersOpen() && $summit->isPublicCategory($category)) return true;

        // check member private categories groups
        if($speaker->Member()->exists() && $groups = $this->getPrivateCategoryGroupsFor($speaker->Member(), $summit))
        {

            foreach($groups as $g)
            {
                if(!$g->hasCategory($category)) continue;
                if(!$g->isSubmissionOpen()) continue;
                return true;
            }
        }
        return false;
    }

    /**
     * @param Member $member
     * @param ISummit $summit
     * @return bool
     */
    public function isPresentationEditionAllowed(Member $member, ISummit $summit)
    {
        if($summit->isPresentationEditionAllowed()) return true;
        $groups = $this->getPrivateCategoryGroupsFor($member, $summit);
        foreach($groups as $g)
        {
            if(!$g->isSubmissionOpen()) continue;
            if($g->Categories()->count() == 0) continue;
            return true;
        }
        return false;
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

            $speaker                               = $creator->getSpeakerProfile();

            $presentation                          = Presentation::create();

            $presentation->Title                   = trim($data['Title']);
            $presentation->Level                   = trim($data['Level']);
            $presentation->ShortDescription        = trim($data['ShortDescription']);
            $presentation->ProblemAddressed        = trim($data['ProblemAddressed']);
            $presentation->AttendeesExpectedLearnt = trim($data['AttendeesExpectedLearnt']);
            $presentation->SelectionMotive         = trim($data['SelectionMotive']);
            $presentation->CategoryID              = intval(trim($data['CategoryID']));

            if(intval($presentation->CategoryID) > 0) {
                $category = PresentationCategory::get()->byID($presentation->CategoryID);
                if(is_null($category)) throw new NotFoundEntityException('category not found!.');
                $limit    = $this->getSubmissionLimitFor($speaker, $category);

                $count    = $summit->isPublicCategory($category) ?
                    (
                        intval($speaker->getPublicCategoryPresentationsBySummit($summit)->count()) +
                        intval($speaker->getPublicCategoryOwnedPresentationsBySummit($summit)->count())
                    ):
                    (
                        intval($speaker->getPrivateCategoryPresentationsBySummit($summit, $summit->getPrivateGroupFor($category))->count()) +
                        intval($speaker->getPrivateCategoryOwnedPresentationsBySummit($summit, $summit->getPrivateGroupFor($category))->count())
                    );

                if ($count >= $limit)
                    throw new EntityValidationException(sprintf('*You reached the limit (%s) of presentations for Category %s', $limit,  $category->Title));
            }

            if(isset($data['OtherTopic']))
                $presentation->OtherTopic = trim($data['OtherTopic']);

            $presentation->SummitID  = $summit->getIdentifier();
            $presentation->CreatorID = $creator->ID;
            $presentation->Progress  = Presentation::PHASE_SUMMARY;
            $presentation->write();

            if(isset($data["PresentationLink"]))
            {
                foreach($data["PresentationLink"] as $id => $val)
                {
                    if(empty($val)) continue;
                    $presentation->Materials()->add(PresentationLink::create(array('Link' => trim($val))));
                }
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
        return $this->tx_manager->transaction(function() use($presentation,  $data){

            $presentation->Title                   = trim($data['Title']);
            $presentation->Level                   = trim($data['Level']);
            $presentation->ShortDescription        = trim($data['ShortDescription']);
            $presentation->ProblemAddressed        = trim($data['ProblemAddressed']);
            $presentation->AttendeesExpectedLearnt = trim($data['AttendeesExpectedLearnt']);
            $presentation->SelectionMotive         = trim($data['SelectionMotive']);

            $presentation->CategoryID              = intval(trim($data['CategoryID']));
            $creator                               = Member::get()->byID($presentation->CreatorID);
            $summit                                = $presentation->Summit();
            $speaker                               = $creator->getSpeakerProfile();

            if(intval($presentation->CategoryID) > 0)
            {
                $category = PresentationCategory::get()->byID($presentation->CategoryID);
                if(is_null($category)) throw new NotFoundEntityException('category not found!.');

                $limit    = $this->getSubmissionLimitFor($speaker, $category);
                $count    = $summit->isPublicCategory($category) ?
                    (
                        intval($speaker->getPublicCategoryPresentationsBySummit($summit)->count()) +
                        intval($speaker->getPublicCategoryOwnedPresentationsBySummit($summit)->count())
                    ):
                    (
                        intval($speaker->getPrivateCategoryPresentationsBySummit($summit, $summit->getPrivateGroupFor($category))->count()) +
                        intval($speaker->getPrivateCategoryOwnedPresentationsBySummit($summit, $summit->getPrivateGroupFor($category))->count())
                    );

                if ($count >= $limit)
                    throw new EntityValidationException(sprintf('You reached the limit (%s) of presentations for Category %s', $limit,  $category->Title));
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

            if($speaker->Presentations()->filter('PresentationID', $presentation->ID)->count() > 0 )
                throw new EntityValidationException('Speaker already assigned to this presentation!.');

            $speaker->Presentations()->add($presentation);
            $speaker->write();

            return $speaker;
        });

    }

    /**
     * @param IPresentation $presentation
     * @param IMessageSenderService $speakers_message_sender
     * @param IMessageSenderService $creator_message_sender
     * @return IPresentation
     */
    public function completePresentation
    (
        IPresentation $presentation,
        IMessageSenderService $speakers_message_sender,
        IMessageSenderService $creator_message_sender
    )
    {
        return $this->tx_manager->transaction(function() use($presentation, $speakers_message_sender, $creator_message_sender)
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

            return $presentation;
        });
    }

}