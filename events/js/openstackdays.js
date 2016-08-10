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
    var slider_count = 4;

    if(window.innerWidth < 450)
        slider_count = 2;

    $(".regular").slick({
        dots: false,
        infinite: true,
        slidesToShow: slider_count,
        slidesToScroll: 2
    });

    var tab = $(window).url_fragment('getParam','tab');
    if(tab !== null) {
        if ($('#'+tab).length) {
            $('.active').removeClass('active');
            $('#'+tab).addClass('active');
            $('.tab-'+tab).addClass('active');
        }
    }

    $('.nav-link').click(function(){
        var tab_id = $($(this).attr('href')).attr('id');
        $(window).url_fragment('setParam','tab', tab_id);
        window.location.hash = $(window).url_fragment('serialize');
    });

    $('#more_events').click(function(ev){
        ev.preventDefault();
        getMoreEvents();
        return false;
    });

    $('.modal').on('hide.bs.modal', function () {
        var iframe_id = $(this).data('section')+'_iframe_'+$(this).data('video_id');
        toggleVideo(iframe_id,'stopVideo');
    });

    $('.modal').on('show.bs.modal', function () {
        var iframe_id = $(this).data('section')+'_iframe_'+$(this).data('video_id');
        toggleVideo(iframe_id,'playVideo');
    });
});

function getMoreEvents() {
    var event_count = $('.featured_event').length;

    $.ajax({
        type: 'GET',
        url: 'api/v1/events/featured?offset='+event_count,
        success: function(result){
            if (result.length) {
                var html = '';
                $.each(result,function(idx,val){
                   html += '<div class="col-md-3 featured_event">';
                   html += val.image + '<p>' + val.title;
                   html += '<span class="font-13">' + val.location + '</span>';
                   html += '<span class="font-12">' + val.date + '</span>';
                   html += '</p></div>';
                });
                var new_div = $(html).hide();
                $('.featured_events').append(new_div);
                new_div.slideDown('slow')
            } else {
                $('.more-events').fadeOut('slow');
            }
        }
    });

}

function toggleVideo(iframe_id,func) {
    var iframe = $('#'+iframe_id)[0].contentWindow;
    iframe.postMessage('{"event":"command","func":"' + func + '","args":""}','*');
}
