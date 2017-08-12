<?php

define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__)))));
if(defined('OPENSTACKID_ENABLED') && OPENSTACKID_ENABLED == true ) {
    require_once __ROOT__ . '/vendor/openid/php-openid/Auth/OpenID/SReg.php';
}


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
class OpenStackIdSecurityController extends CustomPasswordController
{

    private static $allowed_actions = [
        'login',
        'logout',
        'error',
        'ping',
        'changepassword',
        'ChangePasswordForm',
    ];

    /**
     * @var Auth_OpenID_Consumer
     */
    private $openid_consumer;

    /**
     * OpenStackIdSecurityController constructor.
     * @param Auth_OpenID_Consumer $openid_consumer
     */
    public function __construct(Auth_OpenID_Consumer $openid_consumer)
    {
        parent::__construct();
        $this->openid_consumer = $openid_consumer;
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

            // Begin the OpenID authentication process.
            $auth_request = $this->openid_consumer->begin(IDP_OPENSTACKID_URL);
            //remove jainrain nonce
            unset($auth_request->return_to_args['janrain_nonce']);

            // No auth request means we can't begin OpenID.
            if (!$auth_request) {
                throw new Exception("The OpenID authentication failed.");
            }

            if (Auth_OpenID_supportsSReg($auth_request->endpoint)) {
                //SREG
                $sreg_request = Auth_OpenID_SRegRequest::build(array('email', 'fullname'), array('country', 'language'));
                if ($sreg_request) {
                    $auth_request->addExtension($sreg_request);
                }
            } else {
                //AX
                // Create attribute request object
                // See http://code.google.com/apis/accounts/docs/OpenID.html#Parameters for parameters
                // Usage: make($type_uri, $count=1, $required=false, $alias=null)
                $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/contact/email', 1, 1, 'email');
                $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/first', 1, 1, 'firstname');
                $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/last', 1, 1, 'lastname');
                $attribute[] = Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson', 1, 1, 'fullname');
                // Create AX fetch request
                $ax = new Auth_OpenID_AX_FetchRequest();

                // Add attributes to AX fetch request
                foreach ($attribute as $attr) {
                    $ax->add($attr);
                }

                // Add AX fetch request to authentication request
                $auth_request->addExtension($ax);
            }

            //Redirect the user to the OpenID server for authentication .
            // Store the token for this authentication so we can verify the
            // response.

            // For OpenID 1, send a redirect.  For OpenID 2, use a Javascript
            // form to send a POST request to the server.
            if ($auth_request->shouldSendRedirect()) {
                $redirect_url = $auth_request->redirectURL(OpenStackIdCommon::getTrustRoot(), OpenStackIdCommon::getReturnTo());

                // If the redirect URL can't be built, display an error
                // message.
                if (Auth_OpenID::isFailure($redirect_url)) {
                    echo("Could not redirect to server: " . $redirect_url->message);
                    exit();
                }
                // Send redirect.
                header("Location: " . $redirect_url);
                exit();
            }
            // Generate form markup and render it.
            $form_id = 'openid_message';
            $form_html = $auth_request->htmlMarkup(OpenStackIdCommon::getTrustRoot(), OpenStackIdCommon::getReturnTo(), false, array('id' => $form_id));
            // Display an error if the form markup couldn't be generated;
            // otherwise, render the HTML.
            if (Auth_OpenID::isFailure($form_html)) {
                echo("Could not redirect to server: " . $form_html->message);
                exit();
            }
            return $form_html;
        } catch (Exception $ex) {
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
}