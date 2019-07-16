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
use Jumbojett\OpenIDConnectClient;
/**
 * Class OpenStackIdCommon
 */
final class OpenStackIdCommon {

    public static function redirectToSSL($url){
        $dest = str_replace('http:', 'https:', Director::absoluteURL($url));
        // This coupling to SapphireTest is necessary to test the destination URL and to not interfere with tests
        if(!headers_sent()) header("Location: $dest");
        die("<h1>Your browser is not accepting header redirects</h1><p>Please <a href=\"$dest\">click here</a>");
    }

    public static function getRegistrationUrl(string $redirect_uri, bool $append_back_url = true):string{
        $back_url = urlencode(self::getRedirectBackUrl());
        if($append_back_url && !empty($back_url)){
            $append_char = strstr($redirect_uri, '?') == false ? '?' : '&';
            $redirect_uri .= $append_char.'BackURL='.$back_url;
        }
        return sprintf("%s/auth/register?client_id=%s&redirect_uri=%s",IDP_OPENSTACKID_URL,OIDC_CLIENT, urlencode($redirect_uri));
    }

    /**
     * @param string $redirect_uri
     * @param bool $append_back_url
     * @return string
     */
    public static function getRegistrationUrl(string $redirect_uri, bool $append_back_url = true):string{
        $back_url = urlencode(self::getRedirectBackUrl());
        if($append_back_url && !empty($back_url)){
            $append_char = strstr($redirect_uri, '?') == false ? '?' : '&';
            $redirect_uri .= $append_char.'BackURL='.$back_url;
        }
        return sprintf("%s/auth/register?client_id=%s&redirect_uri=%s",IDP_OPENSTACKID_URL,OIDC_CLIENT, urlencode($redirect_uri));
    }

    /**
     * @param string $redirect_uri
     * @param bool $append_back_url
     * @return string
     */
    public static function getLostPasswordUrl(string $redirect_uri = null, bool $append_back_url = true):string{
        $back_url = urlencode(self::getRedirectBackUrl());
        if($append_back_url && !empty($back_url)){
            $append_char = strstr($redirect_uri, '?') == false ? '?' : '&';
            $redirect_uri .= $append_char.'BackURL='.$back_url;
        }
        return sprintf("%s/auth/password/reset?client_id=%s&redirect_uri=%s",IDP_OPENSTACKID_URL,OIDC_CLIENT, urlencode($redirect_uri));
    }

    /**
     * @param \Jumbojett\OpenIDConnectClient $oidc
     */
    public static function saveTokens(OpenIDConnectClient $oidc){
        Session::set("access_token", $oidc->getAccessToken());
        Session::set("refresh_token", $oidc->getRefreshToken());
        Session::set("id_token", $oidc->getIdToken());
        Session::save();
    }


    public function getIdToken():?string{
        return Session::get("id_token");
    }

    /**
     * @return string|null
     * @throws \Jumbojett\OpenIDConnectClientException
     */
    public static function getAccessToken():?string{
        $oidc         = OIDCClientFactory::build();
        $access_token = Session::get("access_token");
        if(empty($access_token)) return null;
        $data = $oidc->introspectToken($access_token);
        if(isset($data->error)) {
            // the token is no longer usable
            $refresh_token = Session::get("refresh_token");
            if(empty($refresh_token)) return null;
            $oidc->refreshToken($refresh_token);
            Session::set("access_token", $oidc->getAccessToken());
            $newRefreshToken =  $oidc->getRefreshToken();
            if(!empty($newRefreshToken))
            Session::set("refresh_token", $newRefreshToken);
            Session::save();
            $access_token = Session::get("access_token");
        }
        return $access_token;
    }
    /**
     * @return string
     */
    public static function getReturnTo()
    {
        $trust_root    = self::getTrustRoot();
        $return_to_url = "{$trust_root}/OpenStackIdAuthenticator?url=/OpenStackIdAuthenticator";
        // check first on session ...
        $back_url      = urlencode(self::getRedirectBackUrl());
        return "{$return_to_url}&BackURL={$back_url}";
    }

    /**
     * @return string
     */
    public static function getRedirectBackUrl(){
        // check first on session ...
        $back_url = Controller::curr()->getSession()->get("BackURL");
        if(empty($back_url))
            $back_url = Controller::curr()->getRequest()->requestVar('BackURL');
        $fragment = Controller::curr()->getRequest()->requestVar('fragment');

        if(empty($back_url))
            $back_url = Director::baseURL();

        if(!empty($fragment))
            $back_url .= $fragment;

        if(!self::isAllowedBackUrl($back_url))
            $back_url = Director::baseURL();

        return Director::absoluteURL($back_url, true);
    }

    public static function getTrustRoot()
    {
        return sprintf("%s://%s", $_SERVER['REQUEST_SCHEME'], $_SERVER['HTTP_HOST']);
    }

    public static function escape($thing) {
        return htmlentities($thing);
    }

    public static function loginMember($member, $back_url){

        $back_url = self::cleanBackUrl($back_url);

        if (!defined('OPENSTACKID_ENABLED') || OPENSTACKID_ENABLED == false){
            $member->login();
            return Controller::curr()->redirect($back_url);
        }

        return
            Controller::curr()->customise(
            [
                    'BackURL' => $back_url,
                    'Member'   => $member
            ])->renderWith(['RegistrationPage_success', 'Page']);
    }

    public static function doLogin($back_url = ''){
        if(empty($back_url)) $back_url = Controller::curr()->getRequest()->getURL(true);
        $back_url = self::cleanBackUrl($back_url);
        return Controller::curr()->redirect('/Security/login/?BackURL='.$back_url);
    }

    public static function doLogout($back_url = ''){
        if(empty($back_url)) $back_url = Controller::curr()->getRequest()->getURL(true);
        $back_url = self::cleanBackUrl($back_url);
        return Controller::curr()->redirect('/Security/logout/?BackURL='.$back_url);
    }

    public static function cleanBackUrl($back_url){
        if(empty($back_url)){
            return Director::baseURL();
        }

        if(!self::isAllowedBackUrl($back_url))
            $back_url = Director::baseURL();

        if($back_url == Director::baseURL()."Security/")
            $back_url = Director::baseURL();
        return $back_url;
    }

    /**
     * @param string $message
     * @param string $back_url
     * @return SS_HTTPResponse
     */
    public static function error($message, $back_url){
        Session::set("Security.Message.message", $message);
        Session::set("Security.Message.type", "bad");
        return Controller::curr()->redirect("Security/error?BackURL={$back_url}");
    }

    public static $AllowedHostNames = [
        CFP_APP_BASE_URL
    ];

    /**
     * @param string $backUrl
     * @return bool
     */
    public static function isAllowedBackUrl(string $backUrl):bool {
        if(!Director::is_site_url($backUrl)){
           // check host name
            $res = parse_url($backUrl);
            if(!$res)
                return false;
            if(!isset($res['host'])) return false;
            if(!isset($res['scheme'])) return false;

            return in_array(sprintf("%s://%s", $res['scheme'], $res['host']), self::$AllowedHostNames);
        }

        return true;
    }
}