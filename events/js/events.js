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
    refresh_future_events_scroll();
    setInterval(refresh_page,60000);

    jQuery('#upcoming_events_filter').change(function(){
        refresh_future_events();
    });

    $('.event-type-link').on('click', function(e) {
        e.preventDefault();
        refresh_future_events($(this));
    })
});

function refresh_page() {
    refresh_future_events();
    refresh_future_summits();
    refresh_past_summits();
}

function refresh_future_events_scroll(){
    var second_column = jQuery('div.events-second-column');
    if(second_column.length>0){
        var upcoming_events_container = jQuery('#upcoming-events');
        if(upcoming_events_container.length>0){
            upcoming_events_container.css('max-height', ( second_column.height() ) +'px');
            upcoming_events_container.css('overflow-y','auto');
            upcoming_events_container.css('overflow-x','none');
        }
    }
}

function refresh_future_events($eventLink) {
    var $loadingIndicator = $('.events-loading');
    $loadingIndicator.removeClass('hidden');

    var filter = 'all';
    if ($eventLink) {
        filter = $eventLink.data('type');

        if ($eventLink.hasClass('event-type-selected')) {
            return;
        }
    }

    var eventsAjaxCall = $.ajax({
        type: "POST",
        url: 'EventHolder_Controller/AjaxFutureEvents',
        data: {filter:filter}
    });

    var counAjaxCall = $.ajax({
        type: "GET",
        url: 'api/v1/events-types/count-by-type'
    });

    $.when(eventsAjaxCall, counAjaxCall)
        .done(function() {

            $('#upcoming-events').html('<div></div>');
            $('div','#upcoming-events'). append(arguments[0][0]);
            refresh_future_events_scroll();

            var countsByEventType = arguments[1][0];
            $('.event-type-link[data-type="All"] span').text(countsByEventType.all);
            $('.event-type-link[data-type="Industry"] span').text(countsByEventType.industry);
            $('.event-type-link[data-type="Meetups"] span').text(countsByEventType.meetups);
            $('.event-type-link[data-type="OpenStack Days"] span').text(countsByEventType.openStackDays);
            $('.event-type-link[data-type="Other"] span').text(countsByEventType.other);

        })
        .always(function(){
            $loadingIndicator.addClass('hidden');
        });

    if ($eventLink != null) {
        $('.event-type-link').removeClass('event-type-selected');
        $eventLink.addClass('event-type-selected')
    }
}

function refresh_future_summits() {
    jQuery.ajax({
        type: "POST",
        url: 'EventHolder_Controller/AjaxFutureSummits',
        success: function(result){
            jQuery('.single-event','#future-summits').remove();
            jQuery('#future-summits').append(result);
        }
    });
}

function refresh_past_summits() {
    jQuery.ajax({
        type: "POST",
        url: 'EventHolder_Controller/AjaxPastSummits',
        success: function(result){
            jQuery('.single-event','#past-summits').remove();
            jQuery('#past-summits').append(result);
        }
    });
}