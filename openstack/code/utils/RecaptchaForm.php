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

        Requirements::javascript(sprintf('https://www.google.com/recaptcha/api.js?hl=%s',$this->getReCaptchaLocale() ));
        Requirements::customScript("var verifyCallback = function(response) {
            $('#g_recaptcha_hidden').val(response);
            $('#g_recaptcha_hidden').valid();
        };
        
        ");
        parent::__construct($controller, $name, $fields, $actions, $validator);
    }

    /**
     * https://developers.google.com/recaptcha/docs/language?hl=en
     */
    private function getReCaptchaLocale(){
        $locale = GetText::current_locale();

        switch ($locale){
            case Locales::German:
                $locale = 'de';
                break;
            case Locales::Chinese:
                $locale = 'zh-CN';
                break;
            case Locales::Taiwanese:
                $locale = 'zh-TW';
                break;
            case Locales::Korean:
                $locale = 'ko';
                break;
            case Locales::Japanese:
                $locale = 'ja';
                break;
            case Locales::Indonesian:
                $locale = 'id';
                break;
            default:
                $locale = 'en';
                break;
        }

        return $locale;
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
            $fields = [
                'secret'    =>  RECAPTCHA_SECRET_KEY,
                'response'  =>  $recaptcha_reponse,
                'remoteip'  =>  $_SERVER['REMOTE_ADDR']
            ];

            $ch = curl_init("https://www.google.com/recaptcha/api/siteverify");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
            $response = json_decode(curl_exec($ch), true);
            curl_close($ch);

            $valid = false;

            if (isset($response['success']) && $response['success'] == true) {
                $valid = true;
            }

            if (isset($response['error-codes']) && is_array($response['error-codes'])) {
                $valid = false;
            }

            if (!$valid) {
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