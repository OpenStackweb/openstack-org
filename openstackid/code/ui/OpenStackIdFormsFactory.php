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

class OpenStackIdFormsFactory {

    /**
     * @param Controller $controller
     * @param string $back_url
     * @return Form
     */
    public static function buildLoginForm(Controller $controller, $back_url){

        if (!defined('OPENSTACKID_ENABLED') || OPENSTACKID_ENABLED == false){
            $form = MemberAuthenticator::get_login_form($controller);

            return $form;
        }
        else{
            $form = new Form($controller, 'OpenStackIdLoginForm',new FieldList(), $actions = new FieldList(
                array(
                    new FormAction('dologin', _t('Member.BUTTONLOGIN', "Log in")),
                )
            ));
            $form->setFormAction("Security/login?BackURL={$back_url}");
            $form->setFormMethod('post');
            return $form;
        }
    }
}