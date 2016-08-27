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
 * Class SummitRegistrationPromoCodeFactory
 */
final class SummitRegistrationPromoCodeFactory
	implements ISummitRegistrationPromoCodeFactory {

	/**
     * @param int $summit_id
	 * @param array $data
	 * @return ISummitRegistrationPromoCode
	 */
	public function buildPromoCode(array $data, $summit_id)
	{
        $code_type = $data['code_type'];

        switch ($code_type) {
            case 'ACCEPTED':
            case 'ALTERNATE':
                $promocode = new SpeakerSummitRegistrationPromoCode();
                break;
            case 'VIP':
            case 'ATC':
            case 'MEDIA ANALYST':
                $promocode = new MemberSummitRegistrationPromoCode();
                break;
            case 'SPONSOR':
                $promocode = new SponsorSummitRegistrationPromoCode();
                break;
        }

		return $this->populatePromoCode($summit_id, $data, $promocode);
	}

    /**
     * @param int $summit_id
     * @param array $data
     * @param ISummitRegistrationPromoCode $promocode
     * @return ISummitRegistrationPromoCode
     */
    public function populatePromoCode($summit_id, array $data, $promocode) {
        $code_type = $data['code_type'];

        switch ($code_type) {
            case 'ACCEPTED':
            case 'ALTERNATE':
                if (isset($data['speaker_id']) && $data['speaker_id']) {
                    $speaker = PresentationSpeaker::get_by_id('PresentationSpeaker',trim($data['speaker_id']));
                    if ($speaker->Exists())
                        $promocode->assignSpeaker($speaker);
                }
                break;
            case 'VIP':
            case 'ATC':
            case 'MEDIA ANALYST':
                if (isset($data['member_id']) && $data['member_id']) {
                    $owner = Member::get_by_id('Member',trim($data['member_id']));
                    if ($owner->Exists())
                        $promocode->assignOwner($owner);
                }
                if (isset($data['first_name']))
                    $promocode->setFirstName(trim($data['first_name']));
                if (isset($data['last_name']))
                    $promocode->setLastName(trim($data['last_name']));
                if (isset($data['email']))
                    $promocode->setEmail(trim($data['email']));
                break;
            case 'SPONSOR':
                if (isset($data['member_id']) && $data['member_id']) {
                    $owner = Member::get_by_id('Member',trim($data['member_id']));
                    if ($owner->Exists())
                        $promocode->assignOwner($owner);
                }
                if (isset($data['company_id']) && $data['company_id']) {
                    $sponsor = Company::get_by_id('Company',trim($data['company_id']));
                    if ($sponsor->Exists())
                        $promocode->assignSponsor($sponsor);
                }
                if (isset($data['first_name']))
                    $promocode->setFirstName(trim($data['first_name']));
                if (isset($data['last_name']))
                    $promocode->setLastName(trim($data['last_name']));
                if (isset($data['email']))
                    $promocode->setEmail(trim($data['email']));
                break;
        }

        $email_sent = (isset($data['email_sent'])) ? 1 : 0;
        $redeemed = (isset($data['redeemed'])) ? 1 : 0;

        $promocode->setCode(trim($data['code']));
        $promocode->setEmailSent($email_sent);
        $promocode->setRedeemed($redeemed);
        $promocode->setSummit(trim($summit_id));
        $promocode->setSource('ADMIN');
        $promocode->setType($code_type);
        $promocode->setCreator(Member::currentUser());

        return $promocode;
    }


}