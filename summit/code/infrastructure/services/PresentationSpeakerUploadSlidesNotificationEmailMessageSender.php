<?php
/**
 * Copyright 2017 OpenStack Foundation
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
 * Class PresentationSpeakerUploadSlidesNotificationEmailMessageSender
 */
final class PresentationSpeakerUploadSlidesNotificationEmailMessageSender
implements IMessageSenderService
{

    /**
     * @param mixed $subject
     * @throws InvalidArgumentException
     * @return void
     */
    public function send($subject)
    {
        if(!is_array($subject)) return;
        if(!isset($subject['Presentations']) || !isset($subject['Speaker']) || !isset($subject['Summit'])) return;

        $presentations = $subject['Presentations'];
        $speaker       = $subject['Speaker'];
        $summit        = $subject['Summit'];

        // check if template exists
        $email_template = PermamailTemplate::get_by_identifier('upload-presentation-slides-email');
        if (is_null($email_template)) {
            return;
        }

        $from    = null;
        $subject = null;
        $email   = EmailFactory::getInstance()->buildEmail($from, $speaker->getEmail(), $subject);

        $email->setUserTemplate('upload-presentation-slides-email');

        $email->populateTemplate
        (
            [

                'Presentations'   => $presentations,
                'Speaker'         => $speaker,
                'Summit'          => $summit,
                // @see class PresentationSlideSubmissionController::presentations
                'UploadSlidesURL' => Director::absoluteURL("submit-slides/presentations")
            ]
        );

        $email->send();

        $speaker->registerUploadSlidesRequestEmail($summit);
    }
}