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
        var is_mobile  = bowser.mobile ||  bowser.tablet;
        var is_ios     = bowser.ios;
        var is_android = bowser.android;

        if (is_mobile &&
            (typeof $("meta[property='al:android:url']").attr("content") != "undefined" || typeof $("meta[property='al:ios:url']").attr("content") != "undefined")){
            var os = is_android ? 'Android' : 'IOS';

            swal({

                title: '<p>Hey there!</p>',
                type: "question",
                html: '<p>Download Mobile Summit App?</p><p><button type="button" class="btn btn-success btn-margin-top-15 btn-continue">Continue to website</button></p>',
                showCancelButton: true,
                confirmButtonText: 'Download for '+os,
                cancelButtonText: "Already Installed? Launch It",
                confirmButtonClass: 'btn btn-primary btn-margin-10',
                cancelButtonClass: 'btn btn-info',
                buttonsStyling: false
            }).then(function() {
                // install
                open(false);
            }, function(dismiss) {
                // dismiss can be 'cancel', 'overlay', 'close', 'timer'
                if (dismiss === 'cancel') {

                    open(true);
                }
            })
        }

        $(document).on('click', '.btn-continue', function() {
            swal.closeModal();
        });

        function open(has_appli) {

            if (is_ios) {
                if(has_appli) {
                    window.location = $("meta[property='al:ios:url']").attr("content") || $("meta[property='al:android:url']").attr("content");

                    setTimeout(function() {
                        // If the user is still here, open the App Store
                        if (!document.webkitHidden) {
                            window.location = 'http://itunes.apple.com/app/id' + $("meta[property='al:ios:app_store_id']").attr("content");
                        }
                    }, 25);
                } else {
                    window.location = 'http://itunes.apple.com/app/id'+$("meta[property='al:ios:app_store_id']").attr("content");
                }

            } else if (is_android) {
                if(has_appli) {
                    window.location = 'intent:/' + $("meta[property='al:android:url']").attr("content").split(':/')[1] + '#Intent;package=' + $("meta[property='al:android:package']").attr("content") + ';scheme=' + $("meta[property='al:android:url']").attr("content").split(':/')[0] + ';launchFlags=268435456;end;';
                }
                else {
                    window.location= 'https://play.google.com/store/apps/details?id='+$("meta[property='al:android:package']").attr("content")+'&hl=en';
                }
            }
        }

    });

})(jQuery);