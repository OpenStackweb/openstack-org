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

require("../../../../gettext/css/languages.css");
require("../../../../gettext/css/flags.css");

// get URI module
import URI from '../../../../themes/openstack/bower_assets/urijs/src/URI';

import React from 'react'
import ReactDOM from 'react-dom'
import LanguageSelector from '../../../../gettext/ui/source/js/components/LanguageSelector'

// add supported languages here

const languages = [
    //"es_ES", // Spanish
    "en_US",
    "zh_CN", // Chinese Traditional
    "zh_TW", // Chinese Mandarin
    "ko_KR", // Korean
    //"ja_JP", // Japanese
    "id_ID", // Indonesian
    //"fr_FR", // French
    "de_DE", // German
    //"ru_RU", // Russian
];
// get language cookie, is none, then default is en_US
var lang = $.cookie('GetTextLocale');
lang     = typeof(lang) == 'undefined' ? 'en_US' : lang;
console.log('language '+lang);
const currentLanguage = lang;

ReactDOM.render(
    <LanguageSelector currentLanguage={currentLanguage} languages={languages} URI={URI}>
    </LanguageSelector>,
    document.getElementById('language-selector')
);