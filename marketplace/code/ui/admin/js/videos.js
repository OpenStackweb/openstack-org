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
(function( $ ){

    var form  = null;

    var methods = {
        init : function(options) {
            form  = $(this);
            if(form.length > 0){

                //first validation method (manual validation)
                $.validator.addMethod("validate_youtube_video", function (value, element, options) {
                    var youtube_id = value;
                    if(youtube_id==='') return true;

                    var m;
                    if (m = youtube_id.match(/^(http|https):\/\/www\.youtube\.com\/.*[?&]v=([^&]+)/i) || youtube_id.match(/^(http|https):\/\/youtu\.be\/([^?]+)/i)) {
                        youtube_id = m[2];
                    }

                    if (!youtube_id.match(/^[a-z0-9_-]{11}$/i)){
                        return false;
                    }
                    return true;
                }, "Your YouTube Video is not valid!");

                var form_validator = form.validate({
                    focusCleanup: true,
                    onkeyup: false
                });

                $(".youtube-video").each(function(){
                    var video            = $(this);
                    var video_max_length = video.attr("data-max-length");
                    $(this).rules('add',{
                        validate_youtube_video:true
                    });
                });
            }
        },
        serialize:function (){
            var is_valid = form.valid();
            if(!is_valid) return false;

            var videos = [];
            //iterate over collection
            $(".youtube-video").each(function(){
                var video                = $(this);
                if(video.val()=='') return;
                var new_video            = {};
                new_video.type_id     = video.attr("data-type-id");
                new_video.length      = video.attr("data-length");
                new_video.title       = video.attr("data-title");
                new_video.description = video.attr("data-description");
                new_video.youtube_id  = video.attr("data-youtube-id");
                videos.push(new_video);
            });
            return videos;
        },
        load: function(videos){
            for(var i in videos){
                var input = $('#video_type_'+videos[i].type_id+'_youtube_id');
                input.val('http://www.youtube.com/watch?v='+videos[i].youtube_id);
                input.attr("data-youtube-id",videos[i].youtube_id)
                input.attr('data-length',videos[i].length);
                input.attr('data-description',videos[i].description);
                input.attr('data-title',videos[i].title);
            }
        }
    }

    $.fn.videos = function(methodOrOptions) {
        if ( methods[methodOrOptions] ) {
            return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
            // Default to "init"
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.videos' );
        }
    };


    function parseresults(data) {
        alert(data);
        var title = data.entry.title.$t;
        var description = data.entry.media$group.media$description.$t;
        var viewcount = data.entry.yt$statistics.viewCount;
        var author = data.entry.author[0].name.$t;
    }
// End of closure.
}( jQuery ));