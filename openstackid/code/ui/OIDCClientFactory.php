<?php
/**
 * Copyright 2019 Open Infrastructure Foundation
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
use Jumbojett\OpenIDConnectClient;
/**
 * Class OIDCClientFactory
 */
final class OIDCClientFactory
{
    const DefaultScopes = 'openid profile email address';

    public static function build():OpenIDConnectClient {
        $oidc = new OpenIDConnectClient(
            IDP_OPENSTACKID_URL,
            OIDC_CLIENT,
            OIDC_CLIENT_SECRET
        );

        $oidc->providerConfigParam([
            'token_endpoint         ' => IDP_OPENSTACKID_URL.'/oauth2/token',
            'authorization_endpoint'  => IDP_OPENSTACKID_URL.'/oauth2/auth',
            'userinfo_endpoint'       => IDP_OPENSTACKID_URL.'/api/v1/users/info',
            'introspection_endpoint'  => IDP_OPENSTACKID_URL.'/oauth2/token/introspection',
            'end_session_endpoint'    => IDP_OPENSTACKID_URL.'/oauth2/end-session',
            'jwks_uri'                => IDP_OPENSTACKID_URL.'/oauth2/certs'
        ]);
        $oidc->setVerifyHost(OIDC_VERIFY_HOST);
        $oidc->setVerifyPeer(OIDC_VERIFY_HOST);

        $scopes = defined('OIDC_SCOPES') ? explode(' ', OIDC_SCOPES):explode(' ', self::DefaultScopes);
        // is openid scope is not present ... add it
        if(!in_array('openid', $scopes)){
            $scopes = array_merge(['openid'], $scopes);
        }
        // is offline_access scope is not present ... add it
        if(!in_array('offline_access', $scopes)){
            $scopes = array_merge(['offline_access'], $scopes);
        }
        $oidc->addScope($scopes);
        $oidc->addAuthParam( ["prompt" => "consent"]);
        $oidc->setRedirectURL(OpenStackIdCommon::getReturnTo());

        return $oidc;
    }
}