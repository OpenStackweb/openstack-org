var schedule_api = riot.observable();
var api_base_url = 'api/v1/summits/@SUMMIT_ID/schedule';

schedule_api.getEventByDay = function (summit_id, day)
{
    schedule_api.trigger('beforeEventsRetrieved',{});
    var url = api_base_url.replace('@SUMMIT_ID', summit_id)+'?day='+day;
    $.ajax({
        type: 'GET',
        url:  url,
        timeout:60000,
        // ifModified: true,
        contentType: "application/json; charset=utf-8",
        success: function (data, textStatus, jqXHR) {
            if(jqXHR.status === 200) {
                data.show_date = false;
            }
            schedule_api.trigger('eventsRetrieved', data, jqXHR.status);

        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        alert('there was an error, please contact your administrator');
    });
}

schedule_api.getEventByLevel = function (summit_id, level)
{
    schedule_api.trigger('beforeEventsRetrieved',{});
    var url = api_base_url.replace('@SUMMIT_ID', summit_id)+'/level?level='+level;
    return $.get(url,function (data) {
        data.show_date = true;
        schedule_api.trigger('eventsRetrieved', data);
    });
}

schedule_api.getEventByTrack = function (summit_id, track)
{
    schedule_api.trigger('beforeEventsRetrieved',{});
    var url = api_base_url.replace('@SUMMIT_ID', summit_id)+'/track?track='+track;
    return $.get(url,function (data) {
        data.show_date = true;
        schedule_api.trigger('eventsRetrieved', data);
    });
}

schedule_api.addEvent2MySchedule = function (summit_id, event_id)
{
    var url = api_base_url.replace('@SUMMIT_ID', summit_id)+'/'+event_id;
    $.ajax({
        type: 'PUT',
        url:  url,
        timeout:10000,
        contentType: "application/json; charset=utf-8",
        success: function (data) {

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

schedule_api.removeEventFromMySchedule = function (summit_id, event_id)
{
    var url = api_base_url.replace('@SUMMIT_ID', summit_id)+'/'+event_id;
    $.ajax({
        type: 'DELETE',
        url:  url,
        timeout:10000,
        contentType: "application/json; charset=utf-8",
        success: function (data) {

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

module.exports = schedule_api;