<?php

define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__)))));
if(defined('OPENSTACKID_ENABLED')) {
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
class OpenStackIdAuthenticator extends Controller
{

    /**
     * @var IMemberRepository
     */
    private $member_repository;

    /**
     * @var Auth_OpenID_Consumer
     */
    private $openid_consumer;

    /**
     * @var Auth_OpenID_OpenIDStore
     */
    private $openid_repository;

    /**
     * OpenStackIdAuthenticator constructor.
     * @param IMemberRepository $member_repository
     * @param Auth_OpenID_OpenIDStore $openid_repository
     * @param Auth_OpenID_Consumer $openid_consumer
     */
    public function __construct
    (
        IMemberRepository $member_repository,
        Auth_OpenID_OpenIDStore $openid_repository,
        Auth_OpenID_Consumer $openid_consumer
    )
    {
        parent::__construct();
        $this->member_repository = $member_repository;
        $this->openid_repository = $openid_repository;
        $this->openid_consumer   = $openid_consumer;
    }

    function index()
    {
        try {

            $member = Member::currentUser();
            
            if ($member){
                // user is already logged in
                return $this->redirect(OpenStackIdCommon::getRedirectBackUrl());
            }
            $query       = Auth_OpenID::getQuery();

            $message     = Auth_OpenID_Message::fromPostArgs($query);
            $nonce       = $message->getArg(Auth_OpenID_OPENID2_NS,'response_nonce');
            list($timestamp, $salt) = Auth_OpenID_splitNonce($nonce);
            $claimed_id  = $message->getArg(Auth_OpenID_OPENID2_NS,'claimed_id');

            SS_Log::log(sprintf('OpenStackIdAuthenticator : id %s - salt %s - timestamp %s - query %s',$claimed_id, $salt, $timestamp, implode(', ',$query)), SS_Log::DEBUG);

            // Complete the authentication process using the server's response.
            $response = $this->openid_consumer->complete(OpenStackIdCommon::getReturnTo());

            if ($response->status == Auth_OpenID_CANCEL) {
                SS_Log ::log('OpenStackIdAuthenticator : Auth_OpenID_CANCEL', SS_Log::WARN);
                throw new Exception('The verification was cancelled. Please try again.');
            } else if ($response->status == Auth_OpenID_FAILURE) {
                SS_Log ::log("OpenStackIdAuthenticator : Auth_OpenID_FAILURE {$response->message}", SS_Log::WARN);
                // delete associations
                SS_Log ::log("OpenStackIdAuthenticator : Auth_OpenID_FAILURE cleaning openid_repository ...", SS_Log::WARN);
                $this->openid_repository->reset();
                throw new Exception("The OpenID authentication failed");

            } else if ($response->status == Auth_OpenID_SUCCESS) {
                SS_Log ::log('OpenStackIdAuthenticator : Auth_OpenID_SUCCESS', SS_Log::DEBUG);
                $openid = $response->getDisplayIdentifier();
                $openid = OpenStackIdCommon::escape($openid);

                if ($response->endpoint->canonicalID) {
                    $openid = escape($response->endpoint->canonicalID);
                }
                //get user info from openid response
                $member = null;
                list($email, $full_name) = $this->getUserProfileInfo($response);
                if(!is_null($email)){
                    //try to get user by email
                    $member = $this->member_repository->findByEmail($email);
                }
                if(!$member){// or by openid
                    $member = Member::get()->filter('IdentityURL', $openid)->first();
                }
                if ($member)
                {
                    $result = $member->canLogIn();
                    if($result->valid())
                    {
                        $member->setIdentityUrl($openid);
                        $member->write();
                        $member->LogIn(true);
                        return $this->redirect(OpenStackIdCommon::getRedirectBackUrl());
                    }
                    throw new Exception("Inactive User!");
                }
                throw new Exception("The OpenID authentication failed: can not find user ".$openid);
            }
        } catch (Exception $ex) {
            SS_Log ::log($ex, SS_Log::WARN);
            return OpenStackIdCommon::error($ex->getMessage(), OpenStackIdCommon::getRedirectBackUrl());
        }
    }

    private function getUserProfileInfo($response)
    {
        if (Auth_OpenID_supportsSReg($response->endpoint)) {
            $sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
            $sreg      = $sreg_resp->contents();
            $email     = @$sreg['email'];
            $full_name = @$sreg['fullname'];
        } else {
            //AX
            // Get registration informations
            $ax = new Auth_OpenID_AX_FetchResponse();
            $obj = $ax->fromSuccessResponse($response);
            $email = $obj->getSingle("http://axschema.org/contact/email");
            $fname = $obj->getSingle("http://axschema.org/namePerson/first", null);
            if (!empty($fname)){
                $lname =  $obj->getSingle("http://axschema.org/namePerson/last", null);
                $full_name = sprintf("%s %s", $fname, $lname);
            }
            else
                $full_name =  $obj->getSingle("http://axschema.org/namePerson", null);
        }
        return array($email, $full_name);
    }
}