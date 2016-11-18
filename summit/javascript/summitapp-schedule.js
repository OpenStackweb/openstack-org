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

    $('.switch_schedule').mouseup(function(){
        $(this).blur();
        if ($(this).hasClass('public')) {
            $(this).html('Switch to Full Schedule');
            $('.schedule_title').html('My Schedule');
            $(this).removeClass('public');

            var filters = getScheduleFilters('private');
        } else {
            $(this).html('Switch to My Schedule');
            $('.schedule_title').html('Schedule');
            $(this).addClass('public');

            var filters = getScheduleFilters('public');
        }

        getSchedule(filters);
    });

});

// this function arranges the filters to filter the events from the server side
function getScheduleFilters(source) {


    var filters = { summit_source: source, event_type: $('.summit_event_type_filter').val()};
    return filters;
}

function applyFilters() {
    // first we show all
    $('.event').show();

    // apply event type filter
    var event_type_id = $('.summit_event_type_filter').val();
    if (event_type_id != -1) {
        $('.event').not('.event_type_'+event_type_id).hide();
    }
}

function toggleCheckboxButton(button_elem) {
    var icon = $('.glyphicon',button_elem);
    button_elem.blur();

    button_elem.toggleClass('checked');

    if(icon.hasClass('glyphicon-unchecked')) {
        icon.removeClass('glyphicon-unchecked').addClass('glyphicon-check');

        button_elem.addClass('btn-primary').removeClass('btn-default');
    } else {
        icon.removeClass('glyphicon-check').addClass('glyphicon-unchecked');
        button_elem.addClass('btn-default').removeClass('btn-primary');

    }
}

function renderEvent(event){
    var row_template = $(
        '<div>' +
            '<div id="event_" class="event" style="">'+
                '<div>'+
                    '<span class="event_type"></span>: <a class="event_title" href=""></a>'+
                    '<div class="presentation_cat"></div>'+
                    '<div class="event_time"></div>'+
                    '<div class="event_location"></div>'+
                '</div>'+
                '<div class="event_speakers">'+
                    '<span class="speaker_name"></span>'+
                '</div>'+
                '<div class="event_details" id="event_details_">'+
                    '<button type="button" class="add_button btn btn-xs " data-event_id=""></button>'+
                    '<a type="button" href="" class="link_button btn btn-xs btn-info">Go to Event</a>'+
                    /*
                        <div class="socials">
                            <div class="fb-like" data-href="http://openstack.org" data-layout="button" data-action="like" data-show-faces="false" data-share="false"></div>
                            <a href="https://twitter.com/share" class="twitter-share-button" data-via="tipit" data-count="none">Tweet</a>
                        </div>
                    */
                    '<hr>'+
                    '<div class="event_details_date"></div>'+
                    '<div class="event_details_loc"></div>'+
                    'Summary:'+
                    '<div class="event_details_desc"></div>'+
                    '<hr>'+
                    'Topics:<br>'+
                    '<span class="event_details_topics"></span>'+
                    '<hr>'+
                    'Speakers:<br>'+
                    '<span class="event_details_speakers"></span>'+
                '</div>'+
            '</div>'+
        '</div>'
    );

    var directives = {
        'div.event@id+'        : 'ID',
        'div.event@style'      : function(a){ return (this.EventType.Color) ? 'background-color:#'+this.EventType.Color : '';},
        'div.event@class+'     : function(a){ return ' event_type_'+this.EventType.ID;},
        'span.event_type'      : 'EventType.Type',
        'a.event_title'        : 'Title',
        'a.event_title@href'   : 'EventLink',
        'div.presentation_cat' : 'EventCategory.Title',
        'div.event_time'       : function(a){ return this.StartTime+' - '+this.EndTime;},
        'div.event_location'   : 'EventLocation',
        '+div.event_speakers'  : function(a){ return (this.EventSpeakers.length) ? '<span class="glyphicon glyphicon-volume-up"></span>' : '';},
        'span.speaker_name'    : { 'Speaker<-EventSpeakers': { '.': 'Speaker.LastName' } },
        '.event_details@id+'   : 'ID',
        '.add_button' : function(a){ return (this.isScheduledEvent) ? 'Remove From My Schedule' : 'Add to My Schedule';},
        '.add_button@data-event_id' : 'ID',
        '.add_button@class+' : function(a){
            if(this.isAttendee) {
                if (this.isScheduledEvent) {
                    return 'remove_from_schedule btn-danger';
                } else {
                    return 'add_to_schedule btn-success';
                }
            } else {
                return 'hidden';
            }
        },
        '.link_button@href' : 'EventLink',
        'div.event_details_date'   : function(a){ return 'Date: '+this.Date+' ('+this.StartTime+' - '+this.EndTime+')';},
        'div.event_details_loc'    : function(a){ return 'Location: '+this.EventLocation;},
        'div.event_details_desc'   : 'Description',
        'span.event_details_topics'   : { 'Topic<-EventTopics': { '.': 'Topic.Title' } },
        'span.event_details_speakers' : { 'Speaker<-EventSpeakers': { '.': 'Speaker.ProfilePic' } }
    };

    return row_template.render(event, directives);
}

function getSchedule(filters) {
    var summit_id = $('#summit_id').val();
    $.ajax({
        type: 'GET',
        url: 'api/v1/summits/'+summit_id+'/schedule?'+$.param(filters),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function (schedule) {
            $('#schedule_container').html('');
            for (var date in schedule) {
                var date_events = schedule[date];

                $('#schedule_container').append('<div class="day">'+date+'</div>');
                $('#schedule_container').append('<div class="event_wrapper"></div>');

                $.each(date_events,function(pos,event){
                    var row = renderEvent(event);
                    $('.event_wrapper','#schedule_container').last().append(row);
                });

                $('#schedule_container').append('<div class="clearfix"></div>');

            }

            setEventHandlers();

            // we apply filters here, not on server
            applyFilters();

            //facebook
            //facebookScript();
            //twitter
            //twitterScript();

        },
        error: function (jqXHR, textStatus, errorThrown) {
            ajaxError(jqXHR, textStatus, errorThrown);
        }
    });

}

function twitterScript() {
    !function(d,s,id){
        var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';
        if(!d.getElementById(id)){
            js=d.createElement(s);
            js.id=id;
            js.src=p+'://platform.twitter.com/widgets.js';
            fjs.parentNode.insertBefore(js,fjs);
        }
    }(document, 'script', 'twitter-wjs');
}

function facebookScript() {
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.4&appId=264587816899119";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
}

function setEventHandlers() {
    $('.event').popover({
        placement: "bottom",
        trigger: "manual",
        html : true,
        content: function() {
            return $(".event_details",this).html();
        }
    }).on("mouseenter", function () {
        var _this = this;
        $(this).popover("show");
        $(this).siblings(".popover").css('left', $(this).position().left+'px');

        $(this).siblings(".popover").on("mouseleave", function () {
            $(_this).popover('hide');
        });

        $('.add_to_schedule').click(function(){
            var event_id = $(this).data('event_id');
            addToSchedule(event_id);
        });

        $('.remove_from_schedule').click(function(){
            var event_id = $(this).data('event_id');
            removeFromSchedule(event_id);
        });

    }).on("mouseleave", function () {
        var _this = this;
        setTimeout(function () {
            if (!$(".popover:hover").length) {
                $(_this).popover("hide")
            }
        }, 100);
    });
}

function addToSchedule(event_id) {
    var summit_id = $('#summit_id').val();
    $.ajax({
        type: 'PUT',
        url: 'api/v1/summits/'+summit_id+'/schedule/'+event_id,
        contentType: "application/json; charset=utf-8",
        success: function (data) {
            var event_wrapper = $('#event_details_'+event_id).parents('.event_wrapper');
            var event_box = $('#event_details_'+event_id).parents('.event');
            $('.add_to_schedule',event_box).replaceWith('<button onclick="removeFromSchedule('+event_id+')" class="btn btn-xs btn-danger remove_from_schedule">Remove From My Schedule</button>');
            $('.add_to_schedule','.popover').replaceWith('<button onclick="removeFromSchedule('+event_id+')" class="btn btn-xs btn-danger remove_from_schedule">Remove From My Schedule</button>');
        }
    });
}

function removeFromSchedule(event_id) {
    var summit_id = $('#summit_id').val();
    $.ajax({
        type: 'DELETE',
        url: 'api/v1/summits/'+summit_id+'/schedule/'+event_id,
        contentType: "application/json; charset=utf-8",
        success: function (data) {
            var event_box = $('#event_details_'+event_id).parents('.event');
            $('.remove_from_schedule',event_box).replaceWith('<button onclick="addToSchedule('+event_id+')" class="btn btn-xs btn-success add_to_schedule">Add To My Schedule</button>');
            $('.remove_from_schedule','.popover').replaceWith('<button onclick="addToSchedule('+event_id+')" class="btn btn-xs btn-success add_to_schedule">Add To My Schedule</button>');
            if (!$('.switch_schedule').hasClass('public')) {
                var filters = getScheduleFilters('private');
                getSchedule(filters);
            }
        }
    });
}