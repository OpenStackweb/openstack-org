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
final class Locales
{
    const English     = 'en_US';
    const Spanish     = 'es_ES';
    const Chinese     = 'zh_CN';
    const Japanese    = 'ja_JP';
    const Korean      = 'ko_KR';
    const Indonesian  = 'id_ID';
    const Russian     = 'ru_RU';
    const German      = 'de_DE';
    const French      = 'fr_FR';
    const Taiwanese   = 'zh_TW';

    public static $valid_locales = [
        self::Spanish,
        self::Taiwanese,
        self::Chinese,
        self::Japanese,
        self::Korean,
        self::English,
        self::Indonesian,
        self::Russian,
        self::German,
        self::French,
    ];

    /**
     * @param string $locale
     * @return string
     */
    public static function process($locale){
        if(in_array($locale, self::$valid_locales)) return $locale;
        return self::English;
    }
}