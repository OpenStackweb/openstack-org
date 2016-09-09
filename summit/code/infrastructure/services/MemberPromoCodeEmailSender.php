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
final class MemberPromoCodeEmailSender implements IMessageSenderService
{

    /**
     * @param mixed $subject
     * @throws Exception
     */
    public function send($subject)
    {
        if(!is_array($subject)) return;
        if(!isset($subject['Summit'])  || !isset($subject['Name']) || !isset($subject['Email']) || !isset($subject['PromoCode']) ) return;

        $summit        = $subject['Summit'];
        $name          = $subject['Name'];
        $email_address = $subject['Email'];
        $promo_code    = $subject['PromoCode'];

        if(!$summit instanceof ISummit) return;
        if(!$promo_code instanceof SummitRegistrationPromoCode) return;


        $email = PermamailTemplate::get()->filter('Identifier', MEMBER_PROMO_CODE_EMAIL)->first();
        if(is_null($email)) throw new Exception(sprintf('Email Template %s does not exists on DB!', MEMBER_PROMO_CODE_EMAIL));

        $email = EmailFactory::getInstance()->buildEmail(MEMBER_NOTIFICATION_PROMO_CODE_EMAIL_FROM, $email_address);

        $schedule_page = SummitAppSchedPage::get()->filter('SummitID', $summit->ID)->first();
        if(is_null($schedule_page)) throw new Exception('Summit Schedule page does not exists!');

        $email->setUserTemplate(MEMBER_PROMO_CODE_EMAIL)->populateTemplate(
            array
            (
                'Name'                 => $name,
                'PromoCode'            => $promo_code->getCode(),
                'Summit'               => $summit,
                'ScheduleMainPageLink' => $schedule_page->getAbsoluteLiveLink(false),
            )
        )
        ->send();
    }
}