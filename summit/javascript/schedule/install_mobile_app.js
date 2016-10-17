/**
 * Copyright 2015 OpenStack Foundation
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

    $(document).ready(function() {

        var is_mobile              = bowser.mobile ||  bowser.tablet;
        var is_ios                 = bowser.ios;
        var is_android             = bowser.android;
        var is_safari              = bowser.safari
        var os_version             = bowser.osversion;
        var browser_version        = bowser.version;
        var current_url            = window.location.href;
        var compare_os_version     = is_ios ? bowser.compareVersions([os_version, '9']): 0
        var uri                    = URI(current_url);

        var has_meta_app_links = (typeof $("meta[property='al:android:url']").attr("content") != "undefined" || typeof $("meta[property='al:ios:url']").attr("content") != "undefined");
        if (is_mobile && has_meta_app_links){
            var os = is_android ? 'Android' : 'IOS';
            // check if we are on RSVP page, dont show it
            console.log('uri path '+uri.path());
            if(uri.path().indexOf('/rsvp') !== -1 ) return;

            var install_mobile_app_action = $.cookie('install_mobile_app_action');
            var show_download_button      = typeof install_mobile_app_action == 'undefined';
            // on ios && safari and ios <= 8 do not show download button
            if(is_ios && is_safari && compare_os_version < 0)
                show_download_button = false

            $('<div></div>')
                .attr('id','install-app-overlay')
                .css('opacity',.9)
                .hover(function(){
                    $(this).addClass('active');
                }, function(){
                    $(this).removeClass('active');
                    setTimeout(function(){
                        $('#install-app-overlay:not(.active)').slideUp();
                    },800);
                }).appendTo('body');



            var install_dialog_html =
                '<div class="row">'+
                '<div class="header-col">'+
                '<a href="/summit">'+
                '<img class="summit-hero-logo" src="/summit/images/logo.png" alt="OpenStack Summit">'+
                '</a>'+
                '</div>'+
                '<div class="header-col">'+
                '<h3>Hey Stacker!</h3>' +
                '</div>'+
                '<div class="header-col right">'+
                '<i title="continue navigating the website" class="fa fa-times btn-continue" aria-hidden="true"></i>' +
                '</div>'+
                '</div>'+
                '<div class="row mobile-message">'+
                '<p>Would you prefer to view this in the OpenStack Summit App?</p>' +
                '</div>'+
                '<div class="row">';

            if (show_download_button) {
                install_dialog_html +=
                    '<button type="button" id="btn-download" title="Download it" class="btn btn-action btn-success btn-xs">Download for ' + os + '</button>' +
                    '<button type="button" id="btn-launch" title="Launch it" class="btn btn-action btn-success btn-xs">Already Installed? Launch It</button>';
            }
            else {
                install_dialog_html += '<button type="button" id="btn-launch" title="Launch it" class="btn btn-action btn-success btn-xs">Launch It on ' + os + '</button>';
            }

            install_dialog_html +=  '</div>';

            $('#install-app-overlay:not(:animated)')
                .addClass('active')
                .html(install_dialog_html)
                .slideDown(1000);
        }

        $(document).on('click', '.btn-continue', function() {
            $('#install-app-overlay').slideUp(1000);
        });

        $(document).on('click', '#btn-download', function() {
            $.cookie('install_mobile_app_action', 'download', { expires: 7 });
            if (is_ios){
                window.location = $("meta[property='al:ios:url']").attr("content") || $("meta[property='al:android:url']").attr("content");

                setTimeout(function() {
                    // If the user is still here, open the App Store
                    if (!document.webkitHidden) {
                        window.location = 'http://itunes.apple.com/app/id' + $("meta[property='al:ios:app_store_id']").attr("content");
                    }
                }, 25);
            }
            else
            {
                window.location= 'https://play.google.com/store/apps/details?id='+$("meta[property='al:android:package']").attr("content")+'&hl=en';
            }
        });

        $(document).on('click', '#btn-launch', function() {
            $.cookie('install_mobile_app_action', 'launch', { expires: 7 });
            if (is_ios){
                window.location = $("meta[property='al:ios:url']").attr("content");
            }
            else
            {
                window.location = 'intent:/' + $("meta[property='al:android:url']").attr("content").split(':/')[1] + '#Intent;package=' + $("meta[property='al:android:package']").attr("content") + ';scheme=' + $("meta[property='al:android:url']").attr("content").split(':/')[0] + ';launchFlags=268435456;end;';
            }
        });

    });

})(jQuery);