<?php
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__)))));
require_once __ROOT__ . '/vendor/openid/php-openid/Auth/OpenID/SReg.php';

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
class OpenStackIdSecurityController extends Security
{

    private static $allowed_actions = array(
        'login',
        'logout',
        'badlogin',
    );

    private $consumer;

    public function __construct()
    {
        parent::__construct();
        $this->consumer = Injector::inst()->get('MyOpenIDConsumer');
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

            if (!Director::is_https()) {
                OpenStackIdCommon::redirectToSSL($_SERVER['REQUEST_URI']);
            }
            if ($this->getRequest()->getVar('BackURL')) {
                Session::set("BackURL", $this->getRequest()->getVar('BackURL'));
            }
            // Begin the OpenID authentication process.
            $auth_request = $this->consumer->begin(IDP_OPENSTACKID_URL);

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
                $redirect_url = $auth_request->redirectURL($this->getTrustRoot(), $this->getReturnTo());

                // If the redirect URL can't be built, display an error
                // message.
                if (Auth_OpenID::isFailure($redirect_url)) {
                    echo("Could not redirect to server: " . $redirect_url->message);
                } else {
                    // Send redirect.
                    header("Location: " . $redirect_url);
                }
            } else {
                // Generate form markup and render it.
                $form_id = 'openid_message';
                $form_html = $auth_request->htmlMarkup(OpenStackIdCommon::getTrustRoot(), OpenStackIdCommon::getReturnTo(), false, array('id' => $form_id));

                // Display an error if the form markup couldn't be generated;
                // otherwise, render the HTML.
                if (Auth_OpenID::isFailure($form_html)) {
                    echo("Could not redirect to server: " . $form_html->message);
                } else {
                    print $form_html;
                }
            }

            exit();

        } catch (Exception $ex) {
            Session::set("Security.Message.message", $ex->getMessage());
            Session::set("Security.Message.type", "bad");
            return $this->redirect("Security/badlogin");
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
        if ($member) $member->logOut();

        // Don't cache the redirect back ever
        HTTP::set_cache_age(0);

        $url = null;

        // In edge-cases, this will be called outside of a handleRequest() context; in that case,
        // redirect to the homepage - don't break into the global state at this stage because we'll
        // be calling from a test context or something else where the global state is inappropraite
        if($this->request) {
            if($this->request->requestVar('BackURL')) {
                $url = $this->request->requestVar('BackURL');
            } else if($this->request->isAjax() && $this->request->getHeader('X-Backurl')) {
                $url = $this->request->getHeader('X-Backurl');
            } else if($this->request->getHeader('Referer')) {
                $url = $this->request->getHeader('Referer');
            }
        }

        if(!$url) $url = Director::baseURL();

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

    public function badlogin()
    {
        return $this->renderWith(
            array('OpenStackIdSecurityController_badlogin', 'Page')
        );

    }

}