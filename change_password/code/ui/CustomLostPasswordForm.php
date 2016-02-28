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
class CustomLostPasswordForm extends HoneyPotForm
{

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    public function __construct($controller, $name, ITransactionManager $tx_manager) {

        $fields = new FieldList(
            new EmailField('Email', _t('Member.EMAIL', 'Email')));

        $actions = new FieldList(
                new FormAction(
                    'forgotPassword',
                    _t('Security.BUTTONSEND', 'Send me the password reset link')
                )
        );
        $this->tx_manager = $tx_manager;
        parent::__construct($controller, $name, $fields, $actions);
    }

    public function forgotPassword($data) {
        $SQL_data = Convert::raw2sql($data);

        $email = $SQL_data['Email'];
        $member = Member::get()->filter('Email', $email)->first();

        // Allow vetoing forgot password requests
        $results = $this->extend('forgotPassword', $member);
        if($results && is_array($results) && in_array(false, $results, true)) {
            return $this->controller->redirect('Security/lostpassword');
        }

        if($member) {
            $token = $this->tx_manager->transaction(function() use($member)
            {
                return $member->generateAutologinTokenAndStoreHash();
            });

            $e = Member_ForgotPasswordEmail::create();
            $e->populateTemplate($member);
            $e->populateTemplate(array(
                'PasswordResetLink' => Security::getPasswordResetLink($member, $token)
            ));
            $e->setTo($member->Email);
            $e->send();
            $this->controller->redirect('Security/passwordsent/' . urlencode($data['Email']));
        }
        elseif($data['Email'])
        {
            // Avoid information disclosure by displaying the same status,
            // regardless wether the email address actually exists
            $this->controller->redirect('Security/passwordsent/' . urlencode($data['Email']));
        } else {
            $this->sessionMessage(
                _t('Member.ENTEREMAIL', 'Please enter an email address to get a password reset link.'),
                'bad'
            );

            $this->controller->redirect('Security/lostpassword');
        }
    }
}