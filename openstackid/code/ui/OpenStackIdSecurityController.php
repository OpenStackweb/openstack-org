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
use Jumbojett\OpenIDConnectClientException;
/**
 * Class OpenStackIdSecurityController
 */
final class OpenStackIdSecurityController extends Security
{

    private static $allowed_actions = [
        'login',
        'logout',
        'error',
        'ping',
        'changepassword',
        'lostpassword',
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function init()
    {
        parent::init();
        if (strpos('/Security/ping',$this->request->getURL()) === false) {
            Page_Controller::AddRequirements();
        }
    }

    /**
     * Show the "login" page
     *
     * @return string Returns the "login" page as HTML code.
     */
    public function login()
    {
        try {
            if (!defined('OPENSTACKID_ENABLED') || OPENSTACKID_ENABLED == false)
                return parent::login();

            $member = Member::currentUser();

            $back_url = OpenStackIdCommon::getRedirectBackUrl();
            if ($member){
                // user is already logged in
                return $this->redirect($back_url);
            }

            if (!Director::is_https()) {
                OpenStackIdCommon::redirectToSSL($_SERVER['REQUEST_URI']);
            }

            // save back url to session
            if(!empty($back_url))
                $this->getSession()->set("BackURL", $back_url);

            OIDCClientFactory::build()->authenticate();

        }
        catch (OpenIDConnectClientException $ex) {
            SS_Log ::log($ex, SS_Log::ERR);
            return OpenStackIdCommon::error($ex->getMessage(), OpenStackIdCommon::getRedirectBackUrl());
        }
        catch (Exception $ex) {
            SS_Log ::log($ex, SS_Log::WARN);
            return OpenStackIdCommon::error($ex->getMessage(), OpenStackIdCommon::getRedirectBackUrl());
        }
    }

    /**
     * Log the currently logged in user out
     *
     * @param bool $redirect Redirect the user back to where they came.
     *                       - If it's false, the code calling logout() is
     *                         responsible for sending the user where-ever
     *                         they should go.
     */
    public function logout($redirect = true)
    {
        if (!defined('OPENSTACKID_ENABLED') || OPENSTACKID_ENABLED == false)
            return parent::logout();

        $member = Member::currentUser();
        if ($member){
            $member_2_delete_id = intval(Session::get('delete_member_id'));
            $member->logOut();
            if($member_2_delete_id > 0 && intval($member->ID) == $member_2_delete_id)
            {
                SapphireTransactionManager::getInstance()->transaction(function() use($member)
                {
                    $member->delete();
                });
            }
        }
        Session::clear('delete_member_id');
        $url = OpenStackIdCommon::getRedirectBackUrl();

        if(strpos($url,'/admin/pages') !== false)
            $url = Director::protocolAndHost();

        $idp= IDP_OPENSTACKID_URL . "/accounts/user/logout";
 $script =       <<<SCRIPT
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <img alt="logout" width="0" height="0" src="{$idp}" id="logout_image" />
  <p>Performing logout...</p>
  <script>
        jQuery(document).ready(function($){
            $("#logout_image").ready(function() {
                window.location ="{$url}";
            });
        });
  </script>
SCRIPT;

        echo $script;

    }

    public function error()
    {
        return $this->customise
        (
           [
                "LoginErrorMessage" => Session::get("Security.Message.message"),
                "OpenStackIdUrl"    => IDP_OPENSTACKID_URL,
                "ReloginUrl"        => OpenStackIdCommon::getRedirectBackUrl()
           ]
        )
        ->renderWith
        (
            array('OpenStackIdSecurityController_error', 'Page')
        );
    }

    public function BackUrl()
    {
        return OpenStackIdCommon::getRedirectBackUrl();
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