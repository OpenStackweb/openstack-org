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

var GetText =  (function () {
    var self           = {};
    var langDictionary = {};

    /*langDictionary['es_ES'] = {
        "This field is required.": "Este campo es obligatorio."
    }*/

    self.addMessages = function (messages) {
        langDictionary = messages;
    };

    function getCurrentLanguage(){
        var lang = $.cookie('GetTextLocale');
        return typeof(lang) == 'undefined' ? 'en_US' : lang;
    }

    self._t = function(msgid){
        var res  = langDictionary[msgid];
        if(typeof(res) == 'undefined')
            res = msgid;
        return res;
    }

    return self;
}());