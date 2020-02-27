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
 * Class OpenStackIdAuthenticator
 */
final class OpenStackIdAuthenticator extends Controller
{

    /**
     * @var IMemberRepository
     */
    private $member_repository;

    /**
     * @var IMemberManager
     */
    private $member_manager;

    public function __construct
    (
        IMemberRepository $member_repository,
        IMemberManager $member_manager
    )
    {
        parent::__construct();
        $this->member_repository = $member_repository;
        $this->member_manager    = $member_manager;
    }

    function index()
    {
        try {

            $member = Member::currentUser();
            
            if ($member){
                // user is already logged in
                return $this->redirect(OpenStackIdCommon::getRedirectBackUrl());
            }

            $oidc = OIDCClientFactory::build();
            if(!$oidc->authenticate())
                throw new OpenIDConnectClientException("failed auth");

            Session::set("access_token", $oidc->getAccessToken());
            Session::set("refresh_token", $oidc->getRefreshToken());
            Session::set("id_token", $oidc->getIdToken());

            $member = $this->member_manager->registerByClaims($oidc->getVerifiedClaims());
            $backUrl = OpenStackIdCommon::getRedirectBackUrl();
            if(!$member->hasMembershipTypeSet()) {
                $type = $this->getRequest()->requestVar("membership-type");
                Session::clear("BackURL");
                if(empty($type)) $type = "foundation";
                $backUrl = Director::absoluteURL(sprintf("/join/register/?membership-type=%s&BackURL=%s", $type, urlencode($backUrl)));
            }
            return $this->redirect($backUrl);
        }
        catch (OpenIDConnectClientException $ex) {
            SS_Log ::log($ex, SS_Log::WARN);
            return OpenStackIdCommon::error($ex->getMessage(), OpenStackIdCommon::getRedirectBackUrl());
        }
        catch (Exception $ex) {
            SS_Log ::log($ex, SS_Log::WARN);
            return OpenStackIdCommon::error($ex->getMessage(), OpenStackIdCommon::getRedirectBackUrl());
        }
    }


}