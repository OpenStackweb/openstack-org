var schedule_admin_view_api                          = riot.observable();
var api_base_url                                     = 'api/v1/summits/@SUMMIT_ID';
var dispatcher                                       = require('./schedule-admin-view-dispatcher.js');
schedule_admin_view_api.RETRIEVED_PUBLISHED_EVENTS   = 'RETRIEVED_PUBLISHED_EVENTS';
schedule_admin_view_api.RETRIEVED_UNPUBLISHED_EVENTS = 'RETRIEVED_UNPUBLISHED_EVENTS';

schedule_admin_view_api.getScheduleByDayAndLocation = function (summit_id, day, location_id)
{
    var url = api_base_url.replace('@SUMMIT_ID', summit_id)+'/schedule?day='+day+'&location='+location_id;
    return $.get(url,function (data) {
        data.summit_id   = summit_id;
        data.location_id = location_id;
        data.day         = day;

        schedule_admin_view_api.trigger(schedule_admin_view_api.RETRIEVED_PUBLISHED_EVENTS, data);
    });
}

schedule_admin_view_api.getUnpublishedEventsBySource = function (summit_id, source, track_list_id, page, page_size)
{
    var url    = api_base_url.replace('@SUMMIT_ID', summit_id)+'/events/unpublished/'+source;
    var params = { 'expand' : 'speakers'};

    if(source === 'tracks' && track_list_id !== '' && typeof track_list_id !== 'undefined' )
        params['track_list_id'] = track_list_id;
    if(page !== '' && typeof page !== 'undefined')
        params['page'] = page;
    if(page_size !== '' && typeof page_size !== 'undefined')
        params['page_size'] = page_size;

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

schedule_admin_view_api.publish = function (summit_id, event)
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
        contentType: "application/json; charset=utf-8",
    })
    .done(function() {
    })
    .fail(function(jqXHR) {
        var responseCode = jqXHR.status;
        if(responseCode == 412) {
            var response = $.parseJSON(jqXHR.responseText);
            $('#event_'+event.id).remove();
            swal("Validation error", response.messages[0], "warning")
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

dispatcher.on(dispatcher.PUBLISHED_EVENTS_FILTER_CHANGE, function(summit_id ,day , location_id){
    schedule_admin_view_api.getScheduleByDayAndLocation(summit_id ,day , location_id);
});

dispatcher.on(dispatcher.UNPUBLISHED_EVENT, function(summit_id, event_id){
    schedule_admin_view_api.unpublish(summit_id, event_id);
});

module.exports = schedule_admin_view_api;