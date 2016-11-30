var schedule_admin_view_api                          = riot.observable();
var api_base_url                                     = 'api/v1/summits/@SUMMIT_ID';
var dispatcher                                       = require('./schedule-admin-view-dispatcher.js');
schedule_admin_view_api.RETRIEVED_PUBLISHED_EVENTS   = 'RETRIEVED_PUBLISHED_EVENTS';
schedule_admin_view_api.RETRIEVED_UNPUBLISHED_EVENTS = 'RETRIEVED_UNPUBLISHED_EVENTS';
schedule_admin_view_api.RETRIEVED_PUBLISHED_SEARCH   = 'RETRIEVED_PUBLISHED_SEARCH';
schedule_admin_view_api.RETRIEVED_EMPTY_SPOTS        = 'RETRIEVED_EMPTY_SPOTS';
schedule_admin_view_api.RETRIEVED_LOCATIONS_BY_DAY   = 'RETRIEVED_LOCATIONS_BY_DAY';

schedule_admin_view_api.getScheduleByDayAndLocation = function (summit_id, day, location_id)
{
    var url = api_base_url.replace('@SUMMIT_ID', summit_id)+'/schedule?day='+day+'&location='+location_id+'&blackouts=1&cache=0';
    return $.get(url,function (data) {
        data.summit_id   = summit_id;
        data.location_id = location_id;
        data.day         = day;

        schedule_admin_view_api.trigger(schedule_admin_view_api.RETRIEVED_PUBLISHED_EVENTS, data);
    });
}

schedule_admin_view_api.getUnpublishedEventsBySource = function (summit_id, source, second_source, status, search_term, order, page, page_size)
{
    var url    = api_base_url.replace('@SUMMIT_ID', summit_id)+'/events/unpublished/'+source;
    var params = { 'expand' : 'speakers'};

    if(source === 'tracks' && second_source !== '' && typeof second_source !== 'undefined' )
        params['track_id'] = second_source;
    if(source === 'track_list' && second_source !== '' && typeof second_source !== 'undefined' )
        params['track_list_id'] = second_source;
    if(source === 'events' && second_source !== '' && typeof second_source !== 'undefined' )
        params['event_type_id'] = second_source;
    if(source !== 'events' && status !== '' && typeof status !== 'undefined' )
        params['status'] = status;

    if(page !== '' && typeof page !== 'undefined')
        params['page'] = page;
    if(page_size !== '' && typeof page_size !== 'undefined')
        params['page_size'] = page_size;

    if(search_term !== '' && typeof search_term !== 'undefined')
        params['search_term'] = search_term;
    if(order !== '' && typeof order !== 'undefined')
        params['order'] = order;

    var query = '';
    for(var key in params)
    {
        if(query !== '') query += '&';
        query += key +'='+params[key];
    }

    if(query !=='' ) url += '?' + query;

    return $.get(url,function (data) {
        schedule_admin_view_api.trigger(schedule_admin_view_api.RETRIEVED_UNPUBLISHED_EVENTS, data);
    });
}

schedule_admin_view_api.getScheduleSearchResults = function (summit_id, term)
{
    var url = api_base_url.replace('@SUMMIT_ID', summit_id)+'/schedule/search?term='+term;
    return $.get(url,function (data) {
        data.summit_id   = summit_id;
        schedule_admin_view_api.trigger(schedule_admin_view_api.RETRIEVED_PUBLISHED_SEARCH, data);
    });
}

schedule_admin_view_api.getScheduleEmptySpots = function (summit_id, days, start_time, end_time, locations, length)
{
    var url    = api_base_url.replace('@SUMMIT_ID', summit_id)+'/schedule/empty_spots';

    var params = {
        days:JSON.stringify(days),
        start_time:start_time,
        end_time:end_time,
        locations:JSON.stringify(locations),
        length:length
    };

    return $.get(url, params, function (data) {
        data.summit_id   = summit_id;
        schedule_admin_view_api.trigger(schedule_admin_view_api.RETRIEVED_EMPTY_SPOTS, data);
    });
}

schedule_admin_view_api.publish = function (summit_id, event, is_published_event)
{
    var url              = api_base_url.replace('@SUMMIT_ID', summit_id)+'/events/'+event.id+'/publish';
    var clone            = jQuery.extend(true, {}, event);

    clone.start_datetime = clone.start_datetime.format('YYYY-MM-DD HH:mm:ss');
    clone.end_datetime   = clone.end_datetime.format('YYYY-MM-DD HH:mm:ss');
    console.log(' start_datetime ' +clone.start_datetime+' end_datetime '+clone.end_datetime);

    $.ajax({
        url : url,
        type: 'PUT',
        data: JSON.stringify(clone),
        contentType: "application/json; charset=utf-8"
    })
    .done(function() {
    })
    .fail(function(jqXHR) {
        var responseCode = jqXHR.status;
        if(responseCode == 412) {
            var response = $.parseJSON(jqXHR.responseText);

            swal({
                title: 'Validation error',
                text: response.messages[0].message,
                type: 'warning'
            },
                function(){
                    if (is_published_event) {
                        $('#event_'+event.id).animate({
                            top: $('#event_'+event.id).attr('prev-pos-top'),
                            height: $('#event_'+event.id).attr('prev-height')
                        });
                    } else {
                        $('.unpublished-events-refresh').click();
                        $('#event_'+event.id).remove();
                    }

                    return false;
                }
            );

            return;
        }
        alert( "There was an error on publishing process, please contact your administrator." );
    });
}

schedule_admin_view_api.unpublish = function (summit_id, event_id){
    var url = api_base_url.replace('@SUMMIT_ID', summit_id)+'/events/'+event_id+'/unpublish';
    console.log('unpublish summit_id '+summit_id+' event_id '+event_id);
    $.ajax({
            url : url,
            type: 'DELETE',
            contentType: "application/json; charset=utf-8",
            dataType: "json",
        })
        .done(function() {
        })
        .fail(function() {
            alert( "There was an error on unpublishing process, please contact your administrator." );
        });
}

schedule_admin_view_api.getLocations = function (summit_id, day){
    var url = api_base_url.replace('@SUMMIT_ID', summit_id)+'/locations';
    console.log('getLocations summit_id '+summit_id+' day '+day);

    return $.get(url+'?day='+day, function (data) {
        data.summit_id   = summit_id;
        schedule_admin_view_api.trigger(schedule_admin_view_api.RETRIEVED_LOCATIONS_BY_DAY, data);
    });
}

dispatcher.on(dispatcher.PUBLISHED_EVENTS_FILTER_CHANGE, function(summit_id ,day , location_id){
    schedule_admin_view_api.getScheduleByDayAndLocation(summit_id ,day , location_id);
});

dispatcher.on(dispatcher.PUBLISHED_EVENTS_SEARCH, function(summit_id ,term){
    schedule_admin_view_api.getScheduleSearchResults(summit_id ,term);
});

dispatcher.on(dispatcher.PUBLISHED_EVENTS_SEARCH_EMPTY, function(summit_id, days, start_time, end_time, locations, length){
    schedule_admin_view_api.getScheduleEmptySpots(summit_id, days, start_time, end_time, locations, length);
});

dispatcher.on(dispatcher.UNPUBLISHED_EVENT, function(summit_id, event_id){
    schedule_admin_view_api.unpublish(summit_id, event_id);
});

dispatcher.on(dispatcher.PUBLISHED_EVENTS_DAY_CHANGE, function(summit_id, day){
    schedule_admin_view_api.getLocations(summit_id, day);
});

module.exports = schedule_admin_view_api;