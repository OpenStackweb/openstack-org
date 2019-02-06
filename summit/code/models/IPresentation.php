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
interface IPresentation extends ISummitEvent
{
    /**
     * SELECTION STATUS (TRACK CHAIRS LIST)
     */
    const SelectionStatus_Accepted   = 'accepted';
    const SelectionStatus_Unaccepted = 'unaccepted';
    const SelectionStatus_Alternate  = 'alternate';

    /**
     * @return bool
     */
    public function creatorBeenEmailed();

    /**
     * @return bool
     */
    public function isNew();

    /**
     * @return mixed
     */
    public function clearBeenEmailed();

    /**
     * @return string
     */
    public function Link();

    /**
     *
     */
    public function assignEventType($type="Presentation");

    /**
     * @return string
     */
    public function getTypeName();

    /**
     * @param string $type
     * @param bool $absolute
     * @return null|string
     */
    public function getLink($type ='voting', $absolute = true);

    /**
     * @return bool
     */
    public function hasVideos();

    /**
     * @param bool $absolute
     * @return null|string
     */
    public function getVideoLink($absolute = true);


    public function getTitleNice();

    /**
     * @return string
     */
    public function EditLink();

    /**
     * Gets a link to edit this presentation
     *
     * @return  string
     */
    public function EditTagsLink();

    /**
     * Gets a link to edit confirmation for this presentation
     *
     * @return  string
     */
    public function EditConfirmLink();

    /**
     * @return string
     */
    public function PreviewLink();

    /**
     * @return string
     */
    public function PreviewIFrameLink();

    /**
     * @return string
     */
    public function EditSpeakersLink();

    /**
     * @return string
     */
    public function DeleteLink();

    /**
     * @return $this
     */
    public function markReceived();

    /**
     * @return int
     */
    public function getProgress();

    /**
     * @param int $progress
     * @throws EntityValidationException
     * @return $this
     */
    public function setProgress($progress);

    /**
     * @return $this
     */
    public function setComplete();


    /**
     * @param IPresentationSpeaker $speaker
     * @return bool
     */
    public function isModerator(IPresentationSpeaker $speaker);

    /**
     * @param IPresentationSpeaker $speaker
     * @return void
     */
    public function removeSpeaker(IPresentationSpeaker $speaker);

    /**
     * @param ITrackQuestionTemplate $question
     * @return ITrackAnswer
     */
    public function findAnswerByQuestion(ITrackQuestionTemplate $question);

    /**
     * @return string
     */
    public function getStatusNice() ;

    /**
     * @return String[]
     */
    public function getWordCloud();

    /**
     * @return bool
     */
    public function useModerators():bool;

    /**
     * @return bool
     */
    public function UseSpeakers():bool;

    /**
     * @return bool
     */
    public function maxModeratorsReached():bool;

    /**
     * @param string $role
     * @return bool
     */
    public function minSpeakerReachedPerRole(string $role):bool;

    /**
     * @param string $role
     * @return bool
     */
    public function maxSpeakerReachedPerRole(string $role):bool;

    /**
     * @return int
     */
    public function getModeratorsCount():int;

    /**
     * @return bool
     */
    public function hasModerators():bool;

    /**
     * @return int
     */
    public function getSpeakersCount():int;

    /**
     * @return bool
     */
    public function hasSpeakers():bool;

    /**
     * @return bool
     */
    public function areSpeakersMandatory():bool;

    /**
     * @return bool
     */
    public function areModeratorsMandatory():bool;

    /**
     * @return int
     */
    public function maxModerators():int;

    /**
     * @return int
     */
    public function maxSpeakers():int;

    /**
     * @return int
     */
    public function minModerators():int;

    /**
     * @return int
     */
    public function minSpeakers():int;

    /**
     * @return string
     */
    public function getNextSpeakerRoleToAdd():string;

    /**
     * @param string $role
     * @return bool
     */
    public function isSpeakerRoleMandatory(string $role):bool;

    /**
     * @param string $role
     * @return bool
     */
    public function existsSpeakersPerRole(string $role): bool;

    /**
     * @param string $role
     * @return int
     */
    public function getMinQtyPerRole(string $role):int;

    /**
     * @return array
     */
    public function getSpeakersAllowedRoles(): array;

    /**
     * @param string $role
     * @return bool
     */
    public function hasSpeakerInRole(string $role):bool;
}