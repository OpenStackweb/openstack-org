<?php
/**
 * Copyright 2014 Openstack Foundation
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

/***
 * Class CustomPasswordController
 */
class CustomPasswordController extends Security
{

    private static $allowed_actions = array(
        'changepassword',
        'lostpassword',
    );

    /**
     * @var PasswordManager
     */
    private $password_manager;

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    public function __construct()
    {
        parent::__construct();
        $this->tx_manager       = SapphireTransactionManager::getInstance();
        $this->password_manager = new PasswordManager($this->tx_manager);
    }



    /**
     * @return string
     */
    public function changepassword()
    {
        return $this->redirect(OpenStackIdCommon::getLostPasswordUrl(
            Director::absoluteURL(sprintf('/Security/login?BackURL=%s', urlencode($_SERVER['HTTP_REFERER']))), false)
        );
    }

    /**
     * Show the "lost password" page
     *
     * @return string Returns the "lost password" page as HTML code.
     */
    public function lostpassword() {

        return $this->redirect(OpenStackIdCommon::getLostPasswordUrl(
            Director::absoluteURL(sprintf('/Security/login?BackURL=%s', urlencode($_SERVER['HTTP_REFERER']))), false)
        );
    }

}