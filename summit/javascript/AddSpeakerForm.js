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
        var cache = {};
        $( "#BootstrapForm_AddSpeakerForm_EmailAddress" ).autocomplete({
            minLength: 2,
            source: function( request, response ) {
                var term = request.term;
                if ( term in cache ) {
                    response( cache[ term ] );
                    return;
                }

                $.getJSON("summit/barcelona-2016/call-for-speakers/manage/13747/speakers/search", request, function( data, status, xhr ) {
                    cache[ term ] = data;
                    response( data );
                });
            },
            select: function( event, ui ) {
                $( "#BootstrapForm_AddSpeakerForm_EmailAddress" ).val(ui.item.name);
                $( "#BootstrapForm_AddSpeakerForm_MemberId" ).val(ui.item.member_id );
                $( "#BootstrapForm_AddSpeakerForm_SpeakerId" ).val(ui.item.speaker_id);
                return false;
            }

        }).data("ui-autocomplete")._renderItem = function(ul, item) {
            // ul is the unordered suggestion list
            // item is a object in the data object that was send to the response function
            // after the JSON request
            // We append a custom formatted list item to the suggestion list
            var html = '<span><img width="50" height="50" src="'+item.pic+'"/>&nbsp;';

            if( item.title != '')
                html+= item.title +'&nbsp;-&nbsp;';

            html += item.name +'&nbsp;('+item.email+')';

            if(item.company != '')
                html += '&nbsp;-&nbsp;' + item.company + '</span>';

            return $("<li></li>").data("item.autocomplete", item).append(html).appendTo(ul);
        };;
    });
})(jQuery);