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

    const AnnouncementEmailAccepted = 'ACCEPTED';
    const AnnouncementEmailRejected = 'REJECTED';
    const AnnouncementEmailAlternate = 'ALTERNATE';
    const AnnouncementEmailAcceptedAlternate = 'ACCEPTED_ALTERNATE';
    const AnnouncementEmailAcceptedRejected = 'ACCEPTED_REJECTED';
    const AnnouncementEmailAlternateRejected = 'ALTERNATE_REJECTED';

    /**
     * @return  string
     */
    public function getName();

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
     * @return $this
     */
    public function registerAnnouncementEmailTypeSent($email_type, $summit_id);

    /**
     * @param int $summit_id
     * @return bool
     */
    public function hasRejectedPresentations($summit_id = null);

    /**
     * @param int $summit_id
     * @return bool
     */
    public function hasApprovedPresentations($summit_id = null);

    /**
     * @param int $summit_id
     * @return bool
     */
    public function hasAlternatePresentations($summit_id = null);

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
     * @param null|int $summit_id
     * @return mixed
     */
    public function PublishedPresentations($summit_id = null);

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
     * @param ISummit $summit
     * @return DataList
     */
    public function getPublicCategoryPresentationsBySummit(ISummit $summit);

    /**
     * @param ISummit $summit
     * @return DataList
     */
    public function getPublicCategoryOwnedPresentationsBySummit(ISummit $summit);

    /**
     * @param ISummit $summit
     * @param PrivatePresentationCategoryGroup $private_group
     * @return DataList
     */
    public function getPrivateCategoryPresentationsBySummit(ISummit $summit, PrivatePresentationCategoryGroup $private_group);

    /**
     * @param ISummit $summit
     * @param PrivatePresentationCategoryGroup $private_group
     * @return DataList
     */
    public function getPrivateCategoryOwnedPresentationsBySummit(ISummit $summit, PrivatePresentationCategoryGroup $private_group);
}