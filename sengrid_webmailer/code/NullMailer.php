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
final class NullMailer extends Mailer
{
    function __construct($mailer = null){
        parent::__construct();
        $this->mailer = $mailer;
    }

    /* Overwriting SilverStripe's Mailer's function */
    function sendHTML($to, $from, $subject, $htmlContent, $attachedFiles = false, $customheaders = false, $plainContent = false, $inlineImages = false){
        $record = SentEmailSendGrid::create(array(
            'To'      => $to,
            'From'    => $from,
            'Subject' => $subject,
            'Body'    => $htmlContent,
            'IsPlain' => 0,
        ));
        if($attachedFiles && is_array($attachedFiles)){
            $record->Attachments = json_encode($attachedFiles);
        }
        $record->write();
    }

    /* Overwriting SilverStripe's Mailer function */
    function sendPlain($to, $from, $subject, $plainContent, $attachedFiles = false, $customheaders = false){
        $record = SentEmailSendGrid::create(array(
            'To'      => $to,
            'From'    => $from,
            'Subject' => $subject,
            'Body'    => $plainContent,
            'IsPlain' => 1,
        ));
        if($attachedFiles && is_array($attachedFiles)){
            $record->Attachments = json_encode($attachedFiles);
        }
        $record->write();
    }

}