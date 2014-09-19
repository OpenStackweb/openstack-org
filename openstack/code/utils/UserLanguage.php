<?php
/**
 * Created by JetBrains PhpStorm.
 * User: smarcet
 * Date: 8/29/13
 * Time: 3:35 PM
 * To change this template use File | Settings | File Templates.
 */

class UserLanguage {

    static private function _parse_accept_language_header(){
        $langs = array();
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            // break up string into pieces (languages and q factors)
            preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);

            if (count($lang_parse[1])) {
                // create a list like "en" => 0.8
                $langs = array_combine($lang_parse[1], $lang_parse[4]);

                // set default to 1 for any without q factor
                foreach ($langs as $lang => $val) {
                    if ($val === '') $langs[$lang] = 1;
                }

                // sort list based on value
                arsort($langs, SORT_NUMERIC);
            }
        }
        else// ??
            $langs = array("en" => 1);
        return $langs;
    }

    static function getCurrentUserLang(){
        $accept_languages = self::_parse_accept_language_header();
        reset($accept_languages);
        $current_lang = key($accept_languages);
        $current_lang = substr($current_lang, 0, 2);

        $languages = array(
            'af'=>'Afrikaans',
            'ar'=>'Arabic',
            'bg'=>'Bulgarian',
            'ca'=>'Catalan',
            'cs'=>'Czech',
            'da'=>'Danish',
            'de'=>'German',
            'el'=>'Greek',
            'en'=>'English',
            'es'=>'Spanish',
            'et'=>'Estonian',
            'fi'=>'Finnish',
            'fr'=>'French',
            'gl'=>'Galician',
            'he'=>'Hebrew',
            'hi'=>'Hindi',
            'hr'=>'Croatian',
            'hu'=>'Hungarian',
            'id'=>'Indonesian',
            'it'=>'Italian',
            'ja'=>'Japanese',
            'ko'=>'Korean',
            'ka'=>'Georgian',
            'lt'=>'Lithuanian',
            'lv'=>'Latvian',
            'ms'=>'Malay',
            'nl'=>'Dutch',
            'no'=>'Norwegian',
            'pl'=>'Polish',
            'pt'=>'Portuguese',
            'ro'=>'Romanian',
            'ru'=>'Russian',
            'sk'=>'Slovak',
            'sl'=>'Slovenian',
            'sq'=>'Albanian',
            'sr'=>'Serbian',
            'sv'=>'Swedis',
            'th'=>'Thai',
            'tr'=>'Turkish',
            'uk'=>'Ukrainian',
            'zh' =>'Chinese',
        );
        if(is_string($current_lang) && array_key_exists($current_lang,$languages))
            return $languages[$current_lang];
        return 'English';
    }
}