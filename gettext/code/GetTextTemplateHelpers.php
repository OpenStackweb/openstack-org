<?php

use Gettext\GettextTranslator;
/**
 * Copyright 2016 OpenStack Foundation
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
class GetTextTemplateHelpers implements TemplateGlobalProvider
{
    const MAX_MSG_ID_LEN = 4096;
    /**
     * @return array
     */
    public static function get_template_global_variables()
    {
        return [
            '_T' => [
                'method' => '_t',
                'casting' => 'HTMLText',
            ],
        ];
    }

    /**
     * @param string $locale
     * @return string
     */
    private static function convertLocale($locale){
        $locale = Locales::process($locale);
        return sprintf('%s.utf8', $locale);
    }


    /**
     * Given the current controller, determine the module name.
     * If the template and controller are in different modules (e.g. themes/),
     * this probably won't produce the expected result on a template.
     *
     * @return string
     */
    public static function module_name()
    {
        $class = Controller::curr()->class;
        $file = SS_ClassLoader::instance()->getItemPath($class);

        if ($file) {
            $dir = dirname($file);
            while (dirname($dir) != BASE_PATH) {
                $dir = dirname($dir);
            }

            $module = basename($dir);
            if ($module == 'openstack') {
                $module = Injector::inst()->get('ViewableData')->ThemeDir();
            }

            return $module;
        }

        return null;
    }

    public static function _t($domain, $msgid){

        // @see https://github.com/php/php-src/commit/6d2dfa4eb047d9a2463ee3873ecea06f6e94fa94
        if(strlen($msgid) > self::MAX_MSG_ID_LEN)
            throw new InvalidArgumentException(sprintf("msgid is too large (%s)!", self::MAX_MSG_ID_LEN));

        $args = [];
        if(func_num_args() > 2)
        {
            $args = func_get_args();
            $args = array_slice($args, 2);
        }

        if(!isset($msgid) || $msgid === false) return "";

        $msgid    = str_replace("\r\n", '', $msgid);
        $msgid    = str_replace("\n", '', $msgid);
        $msgid    = str_replace("\t", '', $msgid);
        $msgid    = str_replace('\"', '"', $msgid);

        $t        = new GettextTranslator();
        $language = self::convertLocale(i18n::get_locale());
        $current_module = self::module_name();

        $path = sprintf("%s/%s/Locale", Director::baseFolder(), $current_module);
        if(!is_dir($path))
            $path = Director::baseFolder().'/gettext/Locale';

        $t->setLanguage($language);
        $t->loadDomain($domain, $path);
        $t->register();

        $msgstr = call_user_func_array("__", array_merge([$msgid], $args));

        return $msgstr;
    }
}