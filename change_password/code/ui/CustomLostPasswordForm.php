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
 * Class CustomLostPasswordForm
 */
class CustomLostPasswordForm extends HoneyPotForm
{

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * CustomLostPasswordForm constructor.
     * @param Controller $controller
     * @param String $name
     * @param ITransactionManager $tx_manager
     */
    public function __construct($controller, $name, ITransactionManager $tx_manager) {

        $fields = new FieldList(new EmailField('Email', _t('Member.EMAIL', 'Email')));

        $actions = new FieldList(
                new FormAction(
                    'forgotPassword',
                    _t('Security.BUTTONSEND', 'Send me the password reset link')
                )
        );

        $validator = new RequiredFields(
            array('Email')
        );

        $this->tx_manager = $tx_manager;
        parent::__construct($controller, $name, $fields, $actions, $validator);
        $this->Fields()->bootstrapIgnore(HoneyPotForm::FieldName)->bootstrapify();
        $this->Actions()->bootstrapify();
        $this->setTemplate("BootstrapForm");
    }

    public function forgotPassword($data) {
        $email = isset($data['Email'])? Convert::raw2sql($data['Email']):null;
        try {
            if(empty($email)) throw new EntityValidationException('Please enter an email address to get a password reset link.');

            $member = Member::get()->filter('Email', $email)->first();

            // Allow vetoing forgot password requests
            $results = $this->extend('forgotPassword', $member);
            if ($results && is_array($results) && in_array(false, $results, true)) {
                return $this->controller->redirect('Security/lostpassword');
            }

            if ($member) {
                $token = $this->tx_manager->transaction(function () use ($member) {
                    if(empty($member->Password)){
                        // fix empty password condition
                        $member->changePassword(substr( md5(rand()), 0, 15));
                    }
                    return $member->generateAutologinTokenAndStoreHash();
                });

                $e = PermamailTemplate::get()->filter('Identifier', MEMBER_FORGOT_PASSWORD_EMAIL_TEMPLATE_ID)->first();
                if(is_null($e))
                    throw new Exception(sprintf('Email Template %s does not exists on DB!', MEMBER_FORGOT_PASSWORD_EMAIL_TEMPLATE_ID));

                $e = EmailFactory::getInstance()->buildEmail("noreply@openstack.org", $member->Email);

                $e->setUserTemplate(MEMBER_FORGOT_PASSWORD_EMAIL_TEMPLATE_ID)->populateTemplate(
                  [
                    'Member'            => $member,
                    'PasswordResetLink' => Security::getPasswordResetLink($member, $token)
                  ])->send();

                $this->controller->redirect('Security/passwordsent/' . urlencode($email));
            }
            // Avoid information disclosure by displaying the same status,
            // regardless weather the email address actually exists
            $this->controller->redirect('Security/passwordsent/' . urlencode($email));
        }
        catch(EntityValidationException $ex1){
            $this->sessionMessage(
                $ex1->getMessage(),
                'bad'
            );
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            $this->controller->redirect('Security/lostpassword');
        }
        catch(Exception $ex){
            $this->sessionMessage(
                'There was an error with your request!',
                'bad'
            );
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            $this->controller->redirect('Security/lostpassword');
        }
    }
}