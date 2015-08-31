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
 * Class SurveyThankYouEmailSenderService
 */
final class SurveyThankYouEmailSenderService implements IMessageSenderService {

    /**
     * @param IEntity $subject
     * @throws InvalidArgumentException
     * @return void
     */
    public function send(IEntity $subject)
    {
       if(! ($subject instanceof ISurvey)) return;
       $current_step = $subject->currentStep();
       $template = $current_step->template();
       if(! ($template instanceof ISurveyThankYouStepTemplate )) return;
       $owner = $subject->createdBy();
       $to    = $owner->getEmail();
       $from  = defined('SURVEY_THANK_U_FROM_EMAIL') ? SURVEY_THANK_U_FROM_EMAIL : Config::inst()->get('Email', 'admin_email');
       $email = EmailFactory::getInstance()->buildEmail($from, $to);
       $email->setUserTemplate('survey-builder-thank-you-step')->populateTemplate(
            array
            (
                'Member'              => $owner,
            )
       )
       ->send();
    }
}