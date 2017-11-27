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

    var $mailer          = null;
    const Header_CC      = 'Cc';
    const Header_BCC     = 'Bcc';
    const Header_ReplyTo = 'Reply-To';

    function __construct($mailer = null){
        parent::__construct();
        $this->mailer = $mailer;
    }

    /**
     * @return SendGrid
     */
    protected function instantiate() {
         return new SendGrid(SENDGRID_API_KEY);
    }

    /* Overwriting SilverStripe's Mailer's function */
    public function sendHTML($to, $from, $subject, $htmlContent, $attachedFiles = false, $customheaders = false, $plainContent = false, $inlineImages = false){

        $mail     = new SendGrid\Mail;
        $sendgrid = $this->instantiate();
        $p        = new \SendGrid\Personalization();

        $to = explode(',',$to);
        foreach($to as $s1){
            $p->addTo(
                new SendGrid\Email(null, trim($s1))
            );
        }

        $this->processEmailHeaders($customheaders, $p, $mail);
        $mail->setFrom(new SendGrid\Email(null, trim($from)));
        $mail->setSubject($subject);
        $mail->addPersonalization($p);

        $content = new \SendGrid\Content("text/html", $htmlContent);

        $mail->addContent($content);

        $this->processAttachments($mail, $attachedFiles);

        // send mail via sendgrid web API
        $response      = $sendgrid->client->mail()->send()->post($mail);
        $response_code = $response->statusCode();
        $response_body = $response->statusCode();

        echo sprintf('sendgrid response - status code %s - body %s', $response_code, $response_body).PHP_EOL;
        return $response_code == 202;
    }

    /* Overwriting SilverStripe's Mailer function */
    public function sendPlain($to, $from, $subject, $plainContent, $attachedFiles = false, $customheaders = false){

        $mail     = new SendGrid\Mail();
        $sendgrid = $this->instantiate();
        $p        = new \SendGrid\Personalization();

        $to = explode(',',$to);
        foreach($to as $s1){
            $p->addTo(
                new SendGrid\Email(null, trim($s1))
            );
        }

        $this->processEmailHeaders($customheaders, $p, $mail);
        $mail->setFrom(new SendGrid\Email(null, trim($from)));
        $mail->setSubject($subject);
        $mail->addPersonalization($p);

        $content = new \SendGrid\Content("text/plain", $plainContent);
        $mail->addContent($content);
        $this->processAttachments($mail, $attachedFiles);

        // send mail via sendgrid web API

        $response      = $sendgrid->client->mail()->send()->post($mail);
        $response_code = $response->statusCode();
        $response_body = $response->statusCode();

        echo sprintf('sendgrid response - status code %s - body %s', $response_code, $response_body).PHP_EOL;
        return $response_code == 202;
    }

    /**
     * @param $headers
     * @param \SendGrid\Personalization $p
     * @param \SendGrid\Mail $mail
     */
    private function processEmailHeaders($headers, \SendGrid\Personalization $p, SendGrid\Mail $mail){
        if($headers && is_array($headers)){
            if(isset($headers[self::Header_CC])){
                foreach (explode(',', $headers[self::Header_CC]) as $cc)
                    $p->addCc(new SendGrid\Email(null, trim($cc)));
            }
            if(isset($headers[self::Header_BCC])){
                foreach (explode(',', $headers[self::Header_BCC]) as $bcc)
                    $p->addBcc(new SendGrid\Email(null, trim($bcc)));
            }
            if(isset($headers[self::Header_ReplyTo])){
                $mail->setReplyTo(new SendGrid\Email(null, $headers[self::Header_ReplyTo]));
            }
        }
    }

    /**
     * @param \SendGrid\Mail $mail
     * @param bool|array $attachedFiles
     */
    private function processAttachments(SendGrid\Mail $mail, $attachedFiles = false ){
        if($attachedFiles && is_array($attachedFiles)){
            foreach ($attachedFiles as $attachedFile){
                $att = new \SendGrid\Attachment();
                $att->setContent(base64_encode($attachedFile['contents']));
                $att->setType($attachedFile['mimetype']);
                $att->setFilename(basename($attachedFile['filename']));
                $att->setDisposition("attachment");
                $mail->addAttachment( $att );
            }
        }
    }

}