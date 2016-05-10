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
     * PresentationManager constructor.
     * @param ISummitRepository $summit_repository
     * @param ISummitEventRepository $event_repository
     * @param ISpeakerRepository $speaker_repository
     * @param IMemberRepository $member_repository
     * @param ITransactionManager $tx_manager
     */
    public function __construct
    (
        ISummitRepository $summit_repository,
        ISummitEventRepository $event_repository,
        ISpeakerRepository $speaker_repository,
        IMemberRepository $member_repository,
        ITransactionManager $tx_manager
    )
    {
        $this->summit_repository  = $summit_repository;
        $this->event_repository   = $event_repository;
        $this->speaker_repository = $speaker_repository;
        $this->member_repository  = $member_repository;
        $this->tx_manager         = $tx_manager;
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
        $max_per_summit     = intval($summit->MaxSubmissionAllowedPerUser);
        $presentation_count = intval($speaker->getPublicCategoryPresentationsBySummit($summit)->count())
                              + intval($speaker->getPublicCategoryOwnedPresentationsBySummit($summit)->count());

        $res                = $presentation_count < $max_per_summit;

        if($speaker->Member()->exists() && $groups = $this->getPrivateCategoryGroupsFor($speaker->Member(), $summit))
        {
            // check private groups
            foreach($groups as $g)
            {
                $max_per_group = intval($g->MaxSubmissionAllowedPerUser);
                if(!$max_per_group) return true; /// infinite
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
            $presentation_count = intval($speaker->getPublicCategoryPresentationsBySummit($summit)->count())
                + intval($speaker->getPublicCategoryOwnedPresentationsBySummit($summit)->count());
            return $presentation_count < $max_per_summit;
        }

        if($speaker->Member()->exists() && $groups = $this->getPrivateCategoryGroupsFor($speaker->Member(), $summit))
        {
            // check private groups
            foreach($groups as $g)
            {
                $max_per_group = intval($g->MaxSubmissionAllowedPerUser);
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

        if($summit->isCallForSpeakersOpen() && $summit->isPublicCategory($category))
            return intval($summit->MaxSubmissionAllowedPerUser);

        if($speaker->Member()->exists() && $groups = $this->getPrivateCategoryGroupsFor($speaker->Member(), $summit)){
            foreach($groups as $g)
            {
                if($g->hasCategory($category))
                {
                    $res = $g->isSubmissionOpen() ? intval($g->MaxSubmissionAllowedPerUser) : -1;
                    break;
                }
            }
        }
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
                $res = $g->isSubmissionOpen() && $g->Categories()->count() > 0;
                if($res) break;
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

        if(is_null($presentation)) return false;
        if(!$presentation->canEdit()) return false;

        $summit = $presentation->Summit();
        if(!$summit->Active) return false;

        $res = false;
        if($summit->isCallForSpeakersOpen()) $res = true;


        // check if the category belongs to a private category group
        $private_groups = $summit->getPrivateCategoryGroups();

        foreach($private_groups as $private_group)
        {
            if($private_group->Categories()->filter('PresentationCategoryID', $presentation->CategoryID)->count() > 0)
            {
                $res = false;
                break;
            }
        }
        // check member private categories groups

        if(!$res && $speaker->Member()->exists())
        {
            $groups = $this->getPrivateCategoryGroupsFor($speaker->Member(), $summit);
            foreach($groups as $g)
            {
                if($g->Categories()->filter('PresentationCategoryID', $presentation->CategoryID)->count() == 0) continue;
                $res = $g->isSubmissionOpen();
                if($res) break;
            }
        }
        return $res;
    }

    /**
     * @param Member $member
     * @param ISummit $summit
     * @return bool
     */
    public function isPresentationEditionAllowed(Member $member, ISummit $summit)
    {
        $res = $summit->isPresentationEditionAllowed();
        if(!$res){
            $groups = $this->getPrivateCategoryGroupsFor($member, $summit);
            foreach($groups as $g)
            {
                $res = $g->isSubmissionOpen() && $g->Categories()->count() > 0;
                if($res) break;
            }
        }
        return $res;
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
            $presentation->CategoryID              = trim($data['CategoryID']);
            $presentation->Progress                = Presentation::PHASE_SUMMARY;

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

            $presentation->CategoryID              = trim($data['CategoryID']);
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
}