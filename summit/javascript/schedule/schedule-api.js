var schedule_api = riot.observable()
var api_base_url = 'api/v1/summits/@SUMMIT_ID/schedule';

schedule_api.getEventByDay = function (summit_id, day)
{
    var url = api_base_url.replace('@SUMMIT_ID', summit_id)+'?day='+day;
    return $.get(url,function (data) {
        schedule_api.trigger('eventsRetrieved', data);
    });
}

module.exports = schedule_api;