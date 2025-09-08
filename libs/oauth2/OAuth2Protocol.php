<?php

namespace Libs\OAuth2;

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

class OAuth2Protocol
{

    const OAuth2Protocol_GrantType_AuthCode = 'authorization_code';
    const OAuth2Protocol_GrantType_Implicit = 'implicit';
    const OAuth2Protocol_GrantType_ResourceOwner_Password = 'password';
    const OAuth2Protocol_GrantType_ClientCredentials = 'client_credentials';
    const OAuth2Protocol_GrantType_RefreshToken = 'refresh_token';
    const OAuth2Protocol_ResponseType_Code = 'code';
    const OAuth2Protocol_ResponseType_Token = 'token';
    const OAuth2Protocol_ResponseType = 'response_type';
    const OAuth2Protocol_ClientId = 'client_id';
    const OAuth2Protocol_UserId = 'user_id';
    const OAuth2Protocol_ClientSecret = 'client_secret';
    const OAuth2Protocol_Token = 'token';
    const OAuth2Protocol_TokenType = 'token_type';
    //http://tools.ietf.org/html/rfc7009#section-2.1
    const OAuth2Protocol_TokenType_Hint = 'token_type_hint';
    const OAuth2Protocol_AccessToken_ExpiresIn = 'expires_in';
    const OAuth2Protocol_RefreshToken = 'refresh_token';
    const OAuth2Protocol_AccessToken = 'access_token';
    const OAuth2Protocol_RedirectUri = 'redirect_uri';
    const OAuth2Protocol_Scope = 'scope';
    const OAuth2Protocol_Audience = 'audience';
    const OAuth2Protocol_State = 'state';
    /**
     * Indicates whether the user should be re-prompted for consent. The default is auto,
     * so a given user should only see the consent page for a given set of scopes the first time
     * through the sequence. If the value is force, then the user sees a consent page even if they
     * previously gave consent to your application for a given set of scopes.
     */
    const OAuth2Protocol_Approval_Prompt = 'approval_prompt';
    const OAuth2Protocol_Approval_Prompt_Force = 'force';
    const OAuth2Protocol_Approval_Prompt_Auto = 'auto';

    /**
     * Indicates whether your application needs to access an API when the user is not present at
     * the browser. This parameter defaults to online. If your application needs to refresh access tokens
     * when the user is not present at the browser, then use offline. This will result in your application
     * obtaining a refresh token the first time your application exchanges an authorization code for a user.
     */
    const OAuth2Protocol_AccessType = 'access_type';
    const OAuth2Protocol_AccessType_Online = 'online';
    const OAuth2Protocol_AccessType_Offline = 'offline';

    const OAuth2Protocol_GrantType = 'grant_type';
    const OAuth2Protocol_Error = 'error';
    const OAuth2Protocol_ErrorDescription = 'error_description';
    const OAuth2Protocol_ErrorUri = 'error_uri';
    const OAuth2Protocol_Error_InvalidRequest = 'invalid_request';
    const OAuth2Protocol_Error_UnauthorizedClient = 'unauthorized_client';
    const OAuth2Protocol_Error_AccessDenied = 'access_denied';
    const OAuth2Protocol_Error_UnsupportedResponseType = 'unsupported_response_type';
    const OAuth2Protocol_Error_InvalidScope = 'invalid_scope';
    const OAuth2Protocol_Error_UnsupportedGrantType = 'unsupported_grant_type';
    const OAuth2Protocol_Error_InvalidGrant = 'invalid_grant';
    //error codes definitions http://tools.ietf.org/html/rfc6749#section-4.1.2.1
    const OAuth2Protocol_Error_ServerError = 'server_error';
    const OAuth2Protocol_Error_TemporallyUnavailable = 'temporally_unavailable';
    //http://tools.ietf.org/html/rfc7009#section-2.2.1
    const OAuth2Protocol_Error_Unsupported_TokenType = ' unsupported_token_type';
    //http://tools.ietf.org/html/rfc6750#section-3-1
    const OAuth2Protocol_Error_InvalidToken = 'invalid_token';
    const OAuth2Protocol_Error_InsufficientScope = 'insufficient_scope';

    public static $valid_responses_types = array(
        self::OAuth2Protocol_ResponseType_Code => self::OAuth2Protocol_ResponseType_Code,
        self::OAuth2Protocol_ResponseType_Token => self::OAuth2Protocol_ResponseType_Token
    );
    public static $protocol_definition = array(
        self::OAuth2Protocol_ResponseType => self::OAuth2Protocol_ResponseType,
        self::OAuth2Protocol_ClientId => self::OAuth2Protocol_ClientId,
        self::OAuth2Protocol_RedirectUri => self::OAuth2Protocol_RedirectUri,
        self::OAuth2Protocol_Scope => self::OAuth2Protocol_Scope,
        self::OAuth2Protocol_State => self::OAuth2Protocol_State
    );

}