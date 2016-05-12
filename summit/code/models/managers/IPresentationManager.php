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
 * Interface IPresentationManager
 */
interface IPresentationManager
{
    /**
     * @param Member $member
     * @param ISummit $summit
     * @return PresentationCategory[]
     */
    public function getAvailableCategoriesFor(Member $member, ISummit $summit);

    /**
     * @param PresentationSpeaker $speaker
     * @param ISummit $summit
     * @return bool
     */
    public function isPresentationSubmissionAllowedFor(PresentationSpeaker $speaker, ISummit $summit);

    /**
     * @param PresentationSpeaker $speaker
     * @param PresentationCategory $category
     * @return int
     */
    public function getSubmissionLimitFor(PresentationSpeaker $speaker, PresentationCategory $category);

    /**
     * @param ISummit $summit
     * @param PresentationSpeaker $speaker
     * @return bool
     */
    public function isCallForSpeakerOpen(ISummit $summit, PresentationSpeaker $speaker);

    /**
     * @param Member $member
     * @param ISummit $summit
     * @return PrivatePresentationCategoryGroup[]
     */
    public function getPrivateCategoryGroupsFor(Member $member, ISummit $summit);

    /**
     * @param Member $member
     * @param ISummit $summit
     * @return bool
     */
    public function isPresentationEditionAllowed(Member $member, ISummit $summit);

    /**
     * @param ISummit $summit
     * @param Member $creator
     * @param array $data
     * @return IPresentation
     */
    public function registerPresentationOn(ISummit $summit, Member $creator, array $data);

    /**
     * @param IPresentation $presentation
     * @param array $data
     * @return IPresentation
     */
    public function updatePresentationSummary(IPresentation $presentation, array $data);

    /**
     * @param $presentation_id
     * @param PresentationSpeaker $speaker
     * @return bool
     */
    public function canEditPresentation($presentation_id, PresentationSpeaker $speaker);

    /**
     * @param PresentationSpeaker $speaker
     * @param Presentation $presentation
     * @return bool
     */
    public function canAddSpeakerOnPresentation(PresentationSpeaker $speaker, Presentation $presentation);

    /**
     * @param int $presentation_id
     * @return void
     */
    public function removePresentation($presentation_id);

    /**
     * @param IPresentation $presentation
     * @param Member $member
     * @param $vote
     */
    public function voteFor(IPresentation $presentation, Member $member, $vote);

    /**
     * @param IPresentation $presentation
     * @param string $email
     * @param Member|null $member
     * @return IPresentationSpeaker
     */
    public function addSpeakerByEmailTo(IPresentation $presentation, $email, Member $member = null);

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
    );
}