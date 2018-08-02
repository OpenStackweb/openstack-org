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
import URI from 'urijs/src/URI';

import React from 'react'
import ReactDOM from 'react-dom'
import LanguageSelector from '../../../../gettext/ui/source/js/components/LanguageSelector'

// add supported languages here

var languages = [
    "en_US",
];
if (window.extraLanguages){
    var extraLanguages = window.extraLanguages;
    for(var i=0; i < extraLanguages.length; i++){
        languages.push(extraLanguages[i]);
    }
}

// get language cookie, is none, then default is en_US
var lang = Cookies.get('GetTextLocale');
lang     = typeof(lang) == 'undefined' ? 'en_US' : lang;
console.log('language '+lang);
const currentLanguage = lang;

ReactDOM.render(
    <LanguageSelector currentLanguage={currentLanguage} languages={languages} URI={URI}>
    </LanguageSelector>,
    document.getElementById('language-selector')
);