<?php

/**
 * Copyright 2017 OpenStack Foundation
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
class GetText extends Object implements TemplateGlobalProvider
{
    static $last_set_locale = [];
    /**
     * @return array
     */
    public static function get_template_global_variables() {
        return array(
            'CurrentLocale' => array(
                'method' => 'current_locale',
                'casting' => 'Text'
            )
        );
    }

    /**
     * Temporary override variable
     *
     * @var boolean
     */
    private static $_override_locale = null;


    public static function init() {

        // Attempt to do pre-emptive i18n bootstrapping, in case session locale is available and
        // only non-sitetree actions will be executed this request (e.g. MemberForm::forgotPassword)
        self::install_locale(true);
    }

    /**
     * Installs the current locale into i18n
     *
     * @param boolean $persist Attempt to persist any detected locale within session / cookies
     */
    public static function install_locale($persist = true) {

        // Ensure the locale is set correctly given the designated parameters
        $locale = self::current_locale($persist);
        if(empty($locale)) return;
        $locale = Locales::process($locale);
        i18n::set_locale($locale);

        // LC_NUMERIC causes SQL errors for some locales (comma as decimal indicator) so skip
        foreach(array(LC_COLLATE, LC_CTYPE, LC_MONETARY, LC_TIME) as $category) {
            setlocale($category, "{$locale}.UTF-8", $locale);
        }

        // Get date/time formats from Zend
        require_once 'Zend/Date.php';
        i18n::config()->date_format = Zend_Locale_Format::getDateFormat($locale);
        i18n::config()->time_format = Zend_Locale_Format::getTimeFormat($locale);
    }

    /**
     * Gets the current locale
     *
     * @param boolean $persist Attempt to persist any detected locale within session / cookies
     * @return string i18n locale code
     */
    public static function current_locale($persist = true) {

        // Check overridden locale
        if(self::$_override_locale) return self::$_override_locale;

        // Check direct request
        $locale = self::get_request_locale();

        // Persistant variables
        if(empty($locale)) $locale = self::get_persist_locale();

        // Check browser headers
        if(empty($locale)) $locale = self::detect_browser_locale();

        // Fallback to default if empty or invalid
        if(empty($locale)) {
            $locale = self::default_locale();
        }

        // Persist locale if requested
        if($persist) self::set_persist_locale($locale);

        return $locale;
    }

    public static function default_locale($domain = null) {
        return self::config()->default_locale;
    }

    public static function set_persist_locale($locale, $key = null) {
        if(empty($key)) $key = self::is_frontend()
            ? self::config()->persist_id
            : self::config()->persist_id_cms;

        // Skip persist if key is unset
        if(empty($key)) {
            return;
        }

        // Save locale
        if($locale) {
            Session::set($key, $locale);
        } else {
            Session::clear($key);
        }

        // Prevent unnecessarily excessive cookie assigment
        if(!headers_sent() && (
                !isset(self::$last_set_locale[$key]) || self::$last_set_locale[$key] !== $locale
            )) {
            self::$last_set_locale[$key] = $locale;
            Cookie::set($key, $locale);
        }
    }

    public static function detect_browser_locale($domain = null) {

        // Check for empty case
        if(empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) return null;

        // Given multiple canditates, narrow down the final result using the client's preferred languages
        $inputLocales = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        if(empty($inputLocales)) return null;

        // Generate mapping of priority => list of locales at this priority
        // break up string into pieces (languages and q factors)
        preg_match_all(
            '/(?<code>[a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(?<priority>1|0\.[0-9]+))?/i',
            $inputLocales,
            $parsedLocales
        );

        $prioritisedLocales = array();
        if (count($parsedLocales['code'])) {
            // create a list like "en" => 0.8
            $parsedLocales = array_combine($parsedLocales['code'], $parsedLocales['priority']);

            // Generate nested list of priorities => [locales]
            foreach ($parsedLocales as $locale => $priority) {
                $priority = empty($priority) ? 1.0 : floatval($priority);
                if(empty($prioritisedLocales[$priority])) {
                    $prioritisedLocales[$priority] = array();
                }
                $prioritisedLocales[$priority][] = $locale;
            }

            // sort list based on value
            krsort($prioritisedLocales, SORT_NUMERIC);
        }

        // Check each requested locale against loaded locales
        foreach ($prioritisedLocales as $priority => $parsedLocales) {
            foreach($parsedLocales as $browserLocale) {
                foreach (self::locales($domain) as $locale) {
                    if (stripos(preg_replace('/_/', '-', $locale), $browserLocale) === 0) {
                        return $locale;
                    }
                }
            }
        }

        return null;
    }


    /**
     * Retrieves the list of locales
     *
     * @param mixed $domain Domain to determine the locales for. If null, the global list be returned.
     * If true, then the current domain will be used.
     * @return array List of locales
     */
    public static function locales($domain = null) {
        if($domain === true) $domain = strtolower($_SERVER['HTTP_HOST']);

        // Check for a domain specific default locale
        if($domain && ($domains = self::domains()) && !empty($domains[$domain])) {
            $info = $domains[$domain];
            if(!empty($info['locales'])) return $info['locales'];
        }

        // Check for configured locales
        if($locales = self::config()->locales) return $locales;

        return array(self::default_locale());
    }

    public static function domains(){
        return [];
    }

    /**
     * Gets the locale requested directly in the request, either via route, post, or query parameters
     *
     * @return string The locale, if available
     */
    public static function get_request_locale() {
        $locale = null;

        // Check controller and current request
        if(Controller::has_curr()) {
            $controller = Controller::curr();
            $request    = $controller->getRequest();
            $locale     = $request->requestVar(self::config()->query_param);
        }

        // Without controller check querystring the old fashioned way
        if(empty($locale) && isset($_REQUEST[self::config()->query_param])) {
            $locale = $_REQUEST[self::config()->query_param];
        }

        return $locale;
    }

    /**
     * Gets the locale currently set within either the session or cookie.
     *
     * @param string $key ID to retrieve persistant locale from. Will automatically detect if omitted.
     * Either GetText::config()->persist_id or GetText::config()->persist_id_cms.
     * @return string|null The locale, if available
     */
    public static function get_persist_locale($key = null) {
        if(empty($key)) $key = self::is_frontend()
            ? self::config()->persist_id
            : self::config()->persist_id_cms;

        // Skip persist if key is unset
        if(empty($key)) {
            return null;
        }

        // check session then cookies
        if($locale = Session::get($key)) return $locale;
        if($locale = Cookie::get($key)) return $locale;
    }

    public static function is_frontend($ignoreController = false) {

        // No controller - Possibly pre-route phase, so check URL
        if($ignoreController || !Controller::has_curr()) {
            if(empty($_SERVER['REQUEST_URI'])) return true;

            // $_SERVER['REQUEST_URI'] indeterminately leads with '/', so trim here
            $base = preg_quote(ltrim(Director::baseURL(), '/'), '/');
            return !preg_match('/^(\/)?'.$base.'admin(\/|$)/i', $_SERVER['REQUEST_URI']);
        }

        // Check if controller is aware of its own role
        $controller = Controller::curr();
        if($controller instanceof ContentController) return true;
        if($controller->hasMethod('isFrontend')) return $controller->isFrontend();

        // Default to return false for any CMS controller
        return !($controller instanceof AdminRootController)
            && !($controller instanceof LeftAndMain);
    }
}