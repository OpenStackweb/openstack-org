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
(function() {

// Localize jQuery variable
    var jQuery;

    var pathArray = document.currentScript.src.split( '/' );
    var protocol = pathArray[0];
    var host    = pathArray[2];
    var origin     = protocol + '//' + host;
    console.log('footer-widget: current origin '+ origin);
    /******** Load jQuery if not present *********/
    if (window.jQuery === undefined || window.jQuery.fn.jquery !== '1.4.2') {
        var script_tag = document.createElement('script');
        script_tag.setAttribute("type","text/javascript");
        script_tag.setAttribute("src",
            "https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js");
        if (script_tag.readyState) {
            script_tag.onreadystatechange = function () { // For old versions of IE
                if (this.readyState == 'complete' || this.readyState == 'loaded') {
                    scriptLoadHandler();
                }
            };
        } else {
            script_tag.onload = scriptLoadHandler;
        }
        // Try to find the head, otherwise default to the documentElement
        (document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
    } else {
        // The jQuery version on the window is the one we want to use
        jQuery = window.jQuery;
        main();
    }

    /******** Called once jQuery has loaded ******/
    function scriptLoadHandler() {
        // Restore $ and window.jQuery to their previous values and store the
        // new jQuery in our local jQuery variable
        jQuery = window.jQuery.noConflict(true);
        // Call our main function
        main();
    }

    /******** Our main function ********/
    function main() {
        jQuery(document).ready(function($) {
            var container = $('#footer_widget_container');
            if(container.length == 0){
                window.alert("In oder to include footer widget , you need a container with id 'footer_widget_container' defined on page!.");
                throw 1;
                return;
            }
            /******* Load CSS *******/
            var css_link = $("<link>", {
                rel: "stylesheet",
                type: "text/css",
                href: origin+"/themes/openstack/css/navigation_footer.css"
            });
            css_link.appendTo('head');

            /******* Load HTML *******/
            var jsonp_url = origin+"/home/getNavigationFooter";

            $.ajax({
                type: 'GET',
                url: jsonp_url,
                async: false,
                jsonpCallback: 'jsonCallback',
                contentType: "application/json",
                dataType: 'jsonp',
                success: function(data) {
                    container.html(data.html);
                },
                error: function(e) {
                    console.log(e.message);
                }
            });
        });
    }

})(); // We call our anonymous function immediately