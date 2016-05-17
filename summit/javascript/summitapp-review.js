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

$(document).ready(function(){
    $(".rating").rating({size:'xs',showCaption:false,showClear:false});

    $('.save').click(function() {
       saveReview($(this).attr('id'));
    });

    $('.rating').on('rating.change', function(event, value, caption) {
        var event_id = $(this).data('event-id');
        $('#'+event_id).prop('disabled',false).text('Save');
    });

    $('.comment').change(function(){
        var event_id = $(this).data('event-id');
        $('#'+event_id).prop('disabled',false).text('Save');
    });

    /*tinyMCE.init({
        selector: "textarea",
        width:      '100%',
        height:     270,
        plugins:    [ "anchor link spellchecker" ],
        toolbar:    "formatselect, fontselect, fontsizeselect, bold, italic, underline, alignleft, aligncenter, alignright, alignjustify, bullist, numlist, outdent, indent, blockquote, undo, redo, removeformat, link, spellchecker",
        statusbar:  false,
        menubar:    false,
    });*/
});

function saveReview(event_id) {
    var rating = $('#rating-'+event_id).val();
    var comment = $('#comment-'+event_id).val();
    var review = {rating: rating, comment: comment};

    if (rating == 0 || comment == ''){
        swal('Error', 'Please fill in the rating and the comment.', 'warning');
    }

    $('#'+event_id).prop('disabled',true);

    $.ajax({
        type: 'POST',
        url:  'api/v1/summits/'+summit_id+'/schedule/'+event_id+'/feedback',
        data: JSON.stringify(review),
        timeout:10000,
        contentType: "application/json; charset=utf-8",
        success: function (data) {
            $('#'+event_id).text('Saved');
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        var http_code = jqXHR.status;
        if(http_code === 401){
            // user lost its session
            alert('you are not logged in!');
            location.reload();
        }
    });
}