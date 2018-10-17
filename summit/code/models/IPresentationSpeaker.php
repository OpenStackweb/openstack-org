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
 * Interface IPresentationSpeaker
 */
interface IPresentationSpeaker extends IEntity
{

    const AnnouncementEmailAccepted          = 'ACCEPTED';
    const AnnouncementEmailRejected          = 'REJECTED';
    const AnnouncementEmailAlternate         = 'ALTERNATE';
    const AnnouncementEmailAcceptedAlternate = 'ACCEPTED_ALTERNATE';
    const AnnouncementEmailAcceptedRejected  = 'ACCEPTED_REJECTED';
    const AnnouncementEmailAlternateRejected = 'ALTERNATE_REJECTED';
    const RoleSpeaker                        = 'SPEAKER';
    const RoleModerator                      = 'MODERATOR';

    /**
     * @return  string
     */
    public function getName();

    /**
     * @return  string
     */
    public function getNameSlug();

    /**
     * Gets a url label for the speaker
     *
     * @return  string
     */

    public function getProfileLink($absolute = true);

    /**
     * @return  string
     */
    public function getCountryName();

    /**
     * @return  string
     */
    public function getCurrentPosition();

    /**
     * @return  string
     */
    public function getTitleNice();

    /**
     * Gets a link to edit this record
     *
     * @return  string
     */
    public function EditLink($presentationID);

    /**
     * Gets a link to delete this presentation
     *
     * @return  string
     */
    public function DeleteLink($presentationID);

    /**
     * Gets a link to the speaker's review page, as seen in the email. Auto authenticates.
     * @param Int $presentationID
     * @return string
     */
    public function ReviewLink($presentationID);

    /**
     * Gets a link to speaker bio form
     *
     * @return  string
     */
    public function BioLink();

    /**
     * @param null|int $summit_id
     * @return mixed
     */
    public function AllPresentations($summit_id = null);

    /**
     * @param null|int $summit_id
     * @return mixed
     */
    public function MyPresentations($summit_id = null);

    /**
     * @param null|int $summit_id
     * @return mixed
     */
    public function OtherPresentations($summit_id = null);

    /**
     * @param null|int $summit_id
     * @return mixed
     */
    public function ModeratorPresentations($summit_id = null);

    /**
     * @param null|int $summit_id
     * @return mixed
     */
    public function OtherModeratorPresentations($summit_id = null);

    /**
     * @param null|int $summit_id
     * @return mixed
     */
    public function getPresentationsCount($summit_id = null);

   /**
     * @return bool
     */
    public function isPendingOfRegistration();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param ICommunityMember $member
     * @return void
     */
    public function associateMember(ICommunityMember $member);

    /**
     * @param int $summit_id
     * @return bool
     */
    public function announcementEmailAlreadySent($summit_id);

    /**
     * @param int $summit_id
     * @return bool
     */
    public function breakoutEmailAlreadySent($summit_id);

    /**
     * @param int $summit_id
     * @return string|null
     */
    public function getAnnouncementEmailTypeSent($summit_id);

    /**
     * @param string $email_type
     * @param int $summit_id
     * @param bool $check_existance
     * @return $this
     */
    public function registerAnnouncementEmailTypeSent($email_type, $summit_id, $check_existance = true);

    /**
     * @param int $summit_id
     * @param string $role
     * @return bool
     */
    public function hasApprovedPresentations($summit_id = null,  $role = IPresentationSpeaker::RoleSpeaker);

    /**
     * @param int $summit_id
     * @param string $role
     * @return bool
     */
    public function hasPublishedPresentations($summit_id = null, $role = IPresentationSpeaker::RoleSpeaker);

    /**
     * @param string $role
     * @return bool
     */
    public function hasHadPublishedPresentations($role = IPresentationSpeaker::RoleSpeaker);

    /**
     * @param ISpeakerSummitRegistrationPromoCode $promo_code
     * @return $this
     */
    public function registerSummitPromoCode(ISpeakerSummitRegistrationPromoCode $promo_code);

    /**
     * @param int $summit_id
     * @return bool
     */
    public function hasSummitPromoCode($summit_id);

    /**
     * @param int $summit_id
     * @return ISpeakerSummitRegistrationPromoCode
     */
    public function getSummitPromoCode($summit_id);

    /**
     * @param int $summit_id
     * @return string
     * @throws Exception
     * @throws ValidationException
     */
    public function getSpeakerConfirmationLink($summit_id);

    /**
     * @param int $summit_id
     * @return string
     */
    public function getOnSitePhoneFor($summit_id);

    /***
     * @param int $summit_id
     * @param string $type
     * @return $this|void
     * @throws Exception
     */
    public function registerBreakOutSent($summit_id, $type);


    /**
     * @param int $summit_id
     * @return bool
     */
    public function hasAssistanceFor($summit_id);

    /**
     * @param int $summit_id
     * @return PresentationSpeakerSummitAssistanceConfirmationRequest
     */
    public function createAssistanceFor($summit_id);

    /**
     * Resets the confirmation request if exists and its not confirmed yet
     * otherwise exception
     * @param int $summit_id
     * @return string
     * @throws Exception
     * @throws ValidationException
     * @throws null
     */
    public function resetConfirmationLink($summit_id);

    /**
     * @param int $summit_id
     * @return bool
     */
    public function hasConfirmedAssistanceFor($summit_id);

    /**
     * @return bool
     */
    public function membershipCreateEmailAlreadySent();

    /**
     * @return $this
     * @throws Exception
     */
    public function registerCreateMembershipSent();

    /**
     * @return bool
     */
    public function hasPendingRegistrationRequest();

    /**
     * @param $promo_code_value
     * @param ISummit $summit
     * @return ISpeakerSummitRegistrationPromoCode
     * @throws EntityValidationException
     * @throws ValidationException
     */
    public function registerSummitPromoCodeByValue($promo_code_value, ISummit $summit);

    /**
     * @param ISelectionPlan $selectionPlan
     * @return ArrayList
     */
    public function getPresentationsByPlan(ISelectionPlan $selectionPlan);

    /**
     * @param null|int $summit_id
     * @param string $role
     * @return ArrayList|bool
     */
    public function UnacceptedPresentations($summit_id = null, $role = IPresentationSpeaker::RoleSpeaker);

    /**
     * @param null|int $summit_id
     * @param string $role
     * @return ArrayList|bool
     */
    public function AcceptedPresentations($summit_id = null, $role = IPresentationSpeaker::RoleSpeaker);

    /**
     * @param null $summit_id
     * @param string $role
     * @param bool $exclude_privates_tracks
     * @param array $excluded_tracks
     * @return bool|DataList
     */
    public function PublishedPresentations($summit_id = null, $role = IPresentationSpeaker::RoleSpeaker,  $exclude_privates_tracks = true, array $excluded_tracks = []);

    /**
     * @param null|int $summit_id
     * @param array $excluded_tracks
     * @return ArrayList
     */
    public function AllPublishedPresentations($summit_id = null, array $excluded_tracks = []);

    /**
     * @param ISummit $summit
     * @return ArrayList
     */
    public function getFeedback(ISummit $summit);

    /**
     * @param ISummit $summit
     * @return float
     */
    public function getAvgFeedback(ISummit $summit);

    /**
     * @param null $summit_id
     * @param string $role
     * @param bool $include_sub_roles
     * @param array $excluded_tracks
     * @return DataList
     */
    public function PublishedRegularPresentations
    (
        $summit_id = null,
        $role = IPresentationSpeaker::RoleSpeaker,
        $include_sub_roles = false,
        array $excluded_tracks = []
    );

    /**
     * @param null $summit_id
     * @param string $role
     * @param bool $include_sub_roles
     * @param array $excluded_tracks
     * @return DataList
     */
    public function hasPublishedRegularPresentations
    (
        $summit_id = null,
        $role = IPresentationSpeaker::RoleSpeaker,
        $include_sub_roles = false,
        array $excluded_tracks = []
    );

    /**
     * @param null $summit_id
     * @param string $role
     * @param bool $include_sub_roles
     * @param array $excluded_tracks
     * @param bool $published_ones
     * @return ArrayList|bool
     */
    public function AlternatePresentations
    (
        $summit_id = null,
        $role = IPresentationSpeaker::RoleSpeaker,
        $include_sub_roles = false,
        array $excluded_tracks = [],
        $published_ones = false
    );

    /**
     * @param int $summit_id
     * @param string $role
     * @param bool $include_sub_roles
     * @param array $excluded_tracks
     * @param bool $published_ones
     * @return bool
     */
    public function hasAlternatePresentations
    (
        $summit_id = null,
        $role = IPresentationSpeaker::RoleSpeaker,
        $include_sub_roles = false,
        array $excluded_tracks = [],
        $published_ones = false
    );

    /**
     * @param null $summit_id
     * @param string $role
     * @param bool $include_sub_roles
     * @param array $excluded_tracks
     * @return ArrayList|bool
     */
    public function RejectedPresentations
    (
        $summit_id = null,
        $role = IPresentationSpeaker::RoleSpeaker,
        $include_sub_roles = false,
        array $excluded_tracks = []
    );

    /**
     * @param null $summit_id
     * @param string $role
     * @param bool $include_sub_roles
     * @param array $excluded_tracks
     * @return int
     */
    public function hasRejectedPresentations
    (
        $summit_id = null,
        $role = IPresentationSpeaker::RoleSpeaker,
        $include_sub_roles = false,
        array $excluded_tracks = []
    );

    /**
     * @param ISummit $summit
     * @return bool
     */
    public function isModeratorFor(ISummit $summit);

    /**
     * @param ISummit $summit
     */
    public function registerUploadSlidesRequestEmail(ISummit $summit);

    /**
     * @param ISummit $summit
     * @return bool
     */
    public function hasUploadSlidesRequestEmail(ISummit $summit);

}