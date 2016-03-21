var schedule_api = riot.observable();
var api_base_url = 'api/v1/summits/@SUMMIT_ID/schedule';

schedule_api.getEventByDay = function (summit_id, day)
{
    schedule_api.trigger('beforeEventsRetrieved',{});
    var url = api_base_url.replace('@SUMMIT_ID', summit_id)+'?day='+day;
    return $.get(url,function (data) {
        schedule_api.trigger('eventsRetrieved', data);
    });
}

schedule_api.getEventByLevel = function (summit_id, level)
{
    schedule_api.trigger('beforeEventsRetrieved',{});
    var url = api_base_url.replace('@SUMMIT_ID', summit_id)+'/level?level='+level;
    return $.get(url,function (data) {
        schedule_api.trigger('eventsRetrieved', data);
    });
}

schedule_api.getEventByTrack = function (summit_id, track)
{
    schedule_api.trigger('beforeEventsRetrieved',{});
    var url = api_base_url.replace('@SUMMIT_ID', summit_id)+'/track?track='+track;
    return $.get(url,function (data) {
        schedule_api.trigger('eventsRetrieved', data);
    });
}

schedule_api.addEvent2MySchedule = function (summit_id, event_id)
{
    var url = api_base_url.replace('@SUMMIT_ID', summit_id)+'/'+event_id;

    $.ajax({
        type: 'PUT',
        url:  url,
        contentType: "application/json; charset=utf-8",
        success: function (data) {
            schedule_api.trigger('eventAdded2MySchedule', event_id);
        }
    });
}

schedule_api.removeEventFromMySchedule = function (summit_id, event_id)
{
    var url = api_base_url.replace('@SUMMIT_ID', summit_id)+'/'+event_id;

    $.ajax({
        type: 'DELETE',
        url:  url,
        contentType: "application/json; charset=utf-8",
        success: function (data) {
            schedule_api.trigger('eventRemovedFromMySchedule', event_id);
        }
    });
}

module.exports = schedule_api;