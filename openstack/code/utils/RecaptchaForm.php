<?php

/**
 * Copyright 2016 OpenStack Foundation
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
class RecaptchaForm extends HoneyPotForm
{
    const RecaptchaFieldName = "recaptcha-div";
    const RecaptchaFieldNameResponse = 'g-recaptcha-response';
    const RecaptchaFieldContent = <<< HTML
<div id="g-recaptcha-container" style="height: 115px !important;padding-top: 15px;" class="g-recaptcha" data-sitekey="%s" data-callback="verifyCallback"></div>
<input type="hidden"name="g_recaptcha_hidden" id="g_recaptcha_hidden">
HTML;

    function __construct($controller, $name, FieldList $fields, FieldList $actions, $validator = null)
    {
        // Guard against automated spam registrations by optionally adding a field
        // that is supposed to stay blank (and is hidden from most humans).
        $fields->push($recaptcha = new LiteralField(self::FieldName, sprintf(self::RecaptchaFieldContent, RECAPTCHA_SITE_KEY)));
        Requirements::javascript('https://www.google.com/recaptcha/api.js');
        Requirements::customScript("var verifyCallback = function(response) {
            $('#g_recaptcha_hidden').val(response);
        };
        
        ");
        parent::__construct($controller, $name, $fields, $actions, $validator);
    }


    public function validate(){
        $res               = parent::validate();
        if(!$res) return false;
        $data              = $this->getData();
        $errors            = [];
        $recaptcha_reponse = $this->getRequest()->requestVar(self::RecaptchaFieldNameResponse);
        if(empty($recaptcha_reponse)){
            $errors[] = [
                'fieldName'   => 'Password',
                'message'     => 'Please confirm that you are not a robot.',
                'messageType' => 'validation',
            ];
            $res = false;

        }
        if($res) {
            $recaptcha = new \ReCaptcha\ReCaptcha(RECAPTCHA_SECRET_KEY);
            $resp = $recaptcha->verify($recaptcha_reponse, $_SERVER['REMOTE_ADDR']);

            if (!$resp->isSuccess()) {
                $errors[] = [
                    'fieldName'   => 'Password',
                    'message'     => 'Recaptcha is invalid!',
                    'messageType' => 'validation',
                ];
                $res = false;
            }
        }

        if(count($errors) > 0){
            Session::set("FormInfo.{$this->FormName()}.errors", $errors);
            Session::set("FormInfo.{$this->FormName()}.data", $data);
        }
        return $res;
    }

}