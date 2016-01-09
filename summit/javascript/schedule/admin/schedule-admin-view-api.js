var schedule_admin_view_api = riot.observable();
var api_base_url = 'api/v1/summits/@SUMMIT_ID';
schedule_admin_view_api.RETRIEVED_UNPUBLISHED_EVENTS = 'RETRIEVED_UNPUBLISHED_EVENTS';

schedule_admin_view_api.getScheduleByDayAndLocation = function (summit_id, day, location_id)
{
    schedule_admin_view_api.trigger('beforeScheduleByDayAndLocationRetrieved',{});
    var url = api_base_url.replace('@SUMMIT_ID', summit_id)+'/schedule?day='+day+'&location='+location_id;
    return $.get(url,function (data) {
        schedule_admin_view_api.trigger('ScheduleByDayAndLocationRetrieved', data);
    });
}

schedule_admin_view_api.getUnpublishedEventsBySource = function (summit_id, source, track_list_id, page, page_size)
{
    schedule_admin_view_api.trigger('beforeUnpublishedEventsBySourceRetrieved',{});
    var url = api_base_url.replace('@SUMMIT_ID', summit_id)+'/events/unpublished/'+source;
    var params = {};
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

module.exports = schedule_admin_view_api;