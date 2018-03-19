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
jQuery(document).ready(function($){
    $('#news-slider').carousel({
       interval: 20000
    });

    window.fbAsyncInit = function() {
        FB.init({
            appId: fbAppId,
            xfbml: true,
            status: true,
            version : 'v2.12'
        });
    };

    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

});

function shareFacebook(url) {
    FB.ui({method: 'share', href: url}, response => {})
}

function shareTwitter(url) {
    const full_url = `https://twitter.com/intent/tweet?url=${url}`;
    const dim = 'left=50,top=50,width=600,height=260,toolbar=1,resizable=0'
    window.open(full_url, 'mywin', dim)
}
