<?php
/**
 * Copyright 2014 Openstack Foundation
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
 * Class FoundationMember
 */
final class FoundationMember
    extends DataExtension
    implements IFoundationMember, ICommunityMember
{

    private static $db = array
    (
        'ShowDupesOnProfile' => "Boolean"
    );

    private static $has_many = array
    (
        'RevocationNotifications' => 'FoundationMemberRevocationNotification',
        'Votes'                   => 'Vote',
        'SummitRegistrationCodes' => 'SummitRegistrationPromoCode',
    );

    private static $defaults = array
    (
        'ShowDupesOnProfile' => TRUE
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->owner->getField('ID');
    }


    public function convert2SiteUser()
    {
        $this->resign(false);
        $this->owner->addToGroupByCode(IFoundationMember::CommunityMemberGroupSlug);
    }

    /**
     * @param bool $remove_affiliation_data
     * @return void
     */
    public function resign($remove_affiliation_data = true)
    {
        // Remove member from Foundation group
        foreach ($this->owner->Groups() as $g) {
            $this->owner->Groups()->remove($g);
        }

        // Remove member managed companies
        foreach ($this->owner->ManagedCompanies() as $c) {
            $this->owner->ManagedCompanies()->remove($c);
        }
        // Remove Member's Legal Agreements
        $legal_agreements = $this->owner->LegalAgreements();
        if ($legal_agreements)
            foreach ($legal_agreements as $document) {
                $document->delete();
            }

        // Remove Member's Affiliations
        if ($remove_affiliation_data) {
            $affiliations = $this->owner->Affiliations();
            if ($affiliations)
                foreach ($affiliations as $affiliation) {
                    $affiliation->delete();
                }
        }
    }

    public function onBeforeDelete()
    {
        $this->resign();
    }

    public function upgradeToFoundationMember()
    {
        if (!$this->isFoundationMember()) {
            // Assign the member to be part of the foundation group
            $this->owner->addToGroupByCode(IFoundationMember::FoundationMemberGroupSlug);
            // Set up member with legal agreement for becoming an OpenStack Foundation Member
            $legalAgreement = new LegalAgreement();
            $legalAgreement->MemberID = $this->owner->ID;
            $legalAgreement->LegalDocumentPageID = 422;
            $legalAgreement->write();
            return true;
        }
        return false;
    }

    public function isFoundationMember()
    {
        $res = $this->owner->inGroup(IFoundationMember::FoundationMemberGroupSlug);
        $legal_agreements = DataObject::get("LegalAgreement", " LegalDocumentPageID=422 AND MemberID =" . $this->owner->ID);
        $res = $res && $legal_agreements->count() > 0;
        return $res;
    }

    /**
     * @param int $latest_election_id
     * @return bool
     */
    public function hasPendingRevocationNotifications($latest_election_id)
    {

    }

    /**
     * @return bool
     */
    public function isCommunityMember()
    {
        $group = $this->owner->inGroup(IFoundationMember::CommunityMemberGroupSlug);
        $is_foundation_member = $this->isFoundationMember();
        return $group || $this->isSpeaker() || $is_foundation_member;
    }

    /**
     * @return bool
     */
    public function isSpeaker()
    {
        $is_speaker = $this->owner->inGroup('speakers');
        return $is_speaker;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return (string)$this->owner->getField('FirstName');
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return (string)$this->owner->getField('Surname');
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return (string)$this->owner->getField('Email');
    }

    /**
     * @return bool
     */
    public function isCandidate()
    {
        return !is_null($this->getCurrentCandidate());
    }

    /**
     * @return ICandidate|null
     */
    public function getCurrentCandidate()
    {
        $res = null;
        $election = ElectionSystem::get()->first();
        if ($election && $election->CurrentElectionID != 0) {
            $current_election = $election->CurrentElection();
            if (!is_null($current_election)) {
                $candidate = Candidate::get()->filter(array(
                    'MemberID' => $this->getIdentifier(),
                    'ElectionID' => $current_election->ID))->first();

                $res = $candidate;
                if (!is_null($candidate)) {
                    UnitOfWork::getInstance()->setToCache($candidate);
                    UnitOfWork::getInstance()->scheduleForUpdate($candidate);
                }
            }
        }
        return $res;
    }

    /**
     * @return bool
     */
    public function hasDeploymentSurveys()
    {
        return DeploymentSurvey::get()->filter(array('MemberID' => $this->getIdentifier()))->count() > 0;
    }

    /**
     * @return bool
     */
    public function hasAppDevSurveys()
    {
        return AppDevSurvey::get()->filter(array('MemberID' => $this->getIdentifier()))->count() > 0;
    }

    /**
     * @return bool
     */
    public function hasSurveys()
    {
        return Survey::get()->filter(array('CreatedByID' => $this->getIdentifier()))->count() > 0;
    }

    /**
     * @return bool
     */
    public function isCompanyAdmin()
    {
        return count($this->owner->getManagedCompanies()) > 0;
    }

    /**
     * @param string $first_name
     * @param string $last_name
     * @return void
     */
    public function updateCompleteName($first_name, $last_name)
    {
        $this->owner->setField('FirstName', $first_name);
        $this->owner->setField('Surname', $last_name);
    }

    /**
     * @param string $email
     * @return void
     */
    public function updateEmail($email)
    {
        $this->owner->setField('Email', $email);
    }

    /**
     * @param string $email
     * @return void
     */
    public function updateSecondEmail($email)
    {
        $this->owner->setField('SecondEmail', $email);
    }

    /**
     * @param string $email
     * @return void
     */
    public function updateThirdEmail($email)
    {
        $this->owner->setField('ThirdEmail', $email);
    }

    /**
     * @param string $shirt_size
     * @param string $statement_interest
     * @param string $bio
     * @param string $gender
     * @param string $food_preference
     * @param string $other_food
     * @return void
     */
    public function updatePersonalInfo($shirt_size, $statement_interest, $bio, $gender, $food_preference, $other_food)
    {
        $this->owner->setField('ShirtSize', $shirt_size);
        $this->owner->setField('StatementOfInterest', $statement_interest);
        $this->owner->setField('Bio', $bio);
        $this->owner->setField('Gender', $gender);
        $this->owner->setField('FoodPreference', $food_preference);
        $this->owner->setField('OtherFood', $other_food);
    }

    /**
     * @param string $projects
     * @param string $other_projects
     * @return void
     */
    public function updateProjects($projects, $other_projects)
    {
        $this->owner->setField('Projects', $projects);
        $this->owner->setField('OtherProject', $other_projects);
    }

    /**
     * @param string $irc_handle
     * @param string $twitter_name
     * @param string $linkedin_profile
     * @return void
     */
    public function updateSocialInfo($irc_handle, $twitter_name, $linkedin_profile)
    {
        $this->owner->setField('IRCHandle', $irc_handle);
        $this->owner->setField('TwitterName', $twitter_name);
        $this->owner->setField('LinkedInProfile', $linkedin_profile);
    }

    /**
     * @param string $address
     * @param string $suburb
     * @param string $state
     * @param string $postcode
     * @param string $city
     * @param string $country
     * @return void
     */
    public function updateAddress($address, $suburb, $state, $postcode, $city, $country)
    {
        $this->owner->setField('Address', $address);
        $this->owner->setField('Suburb', $suburb);
        $this->owner->setField('State', $state);
        $this->owner->setField('Postcode', $postcode);
        $this->owner->setField('City', $city);
        $this->owner->setField('Country', $country);
    }

    /**
     * @param $photo_id
     * @return mixed
     */
    public function updateProfilePhoto($photo_id)
    {
        $this->owner->setField('PhotoID', $photo_id);
    }

    /**
     * @param bool $show
     * @return void
     */
    public function showDupesOnProfile($show)
    {
        $this->owner->setField('ShowDupesOnProfile', $show);
    }

    /**
     * @return bool
     */
    public function shouldShowDupesOnProfile()
    {
        return $this->owner->getField('ShowDupesOnProfile');
    }

    /**
     * @param int $summit_id
     * @return bool
     */
    public function hasPromoRegistrationCode($summit_id)
    {
        $code = $this->getPromoCodeForSummit($summit_id);
        return !is_null($code);
    }

    /**
     * @param int $summit_id
     * @return ISummitRegistrationPromoCode
     */
    public function getPromoCodeForSummit($summit_id)
    {
        return $this->owner->SummitRegistrationCodes()->filter('SummitID',$summit_id)->first();
    }

    /**
     * @param ISummitRegistrationPromoCode $promo_code
     * @return $this
     */
    public function registerPromoCode(ISummitRegistrationPromoCode $promo_code)
    {
        $promo_code->assignOwner($this->owner);
        return $this;
    }
}