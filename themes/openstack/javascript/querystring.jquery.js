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
(function($) {
    $.QueryString = (function(a) {
        if (a == "") return {};
        var b = {};
        for (var i = 0; i < a.length; ++i)
        {
            var p = a[i].split('=');

            if (p.length != 2) continue;

            var key = decodeURIComponent(p[0]);
            var val = decodeURIComponent(p[1].replace(/\+/g, " "));
            if(b[key] !== undefined && typeof b[key] === 'string'){
                b[key] = [b[key]];
            }
            if( Object.prototype.toString.call( b[key] ) === '[object Array]' ){
                b[key].push(val);
                continue;
            }
            b[key] = val;
        }
        return b;
    })(window.location.search.substr(1).split('&'))
})(jQuery);
