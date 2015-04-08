<?php
/**
 * Copyright 2015 Openstack Foundation
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
 * Class SendGridWebMailer
 */
class SendGridWebMailer extends Mailer {

    var $mailer = null;

    function __construct($mailer = null){
        parent::__construct();
        $this->mailer = $mailer;
    }
    protected function instanciate() {
        $sendgrid = new SendGrid(SMTPMAILER_USERNAME, SMTPMAILER_PASSWORD);
        return $sendgrid;
    }

    /* Overwriting SilverStripe's Mailer's function */
    function sendHTML($to, $from, $subject, $htmlContent, $attachedFiles = false, $customheaders = false, $plainContent = false, $inlineImages = false){
        $mail = new SendGrid\Email();
        $sendgrid = $this->instanciate();

        $to = explode(',',$to);
        foreach($to as $s1){
            $mail->addTo(trim($s1));
        }

        $mail->setFrom(trim($from));
        $mail->setSubject($subject);
        $mail->setHtml($htmlContent);

        $sendgrid->send($mail);                        // send mail via sendgrid web API
    }
    /* Overwriting SilverStripe's Mailer function */
    function sendPlain($to, $from, $subject, $plainContent, $attachedFiles = false, $customheaders = false){

        $mail = new SendGrid\Email();
        $sendgrid = $this->instanciate();

        $to = explode(',',$to);
        foreach($to as $s1){
            $mail->addTo(trim($s1));
        }

        $mail->setFrom(trim($from));
        $mail->setSubject($subject);
        $mail->setText($plainContent);

        $sendgrid->send($mail);
    }


}