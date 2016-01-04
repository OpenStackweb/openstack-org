var schedule_admin_view_api = riot.observable();
var api_base_url = 'api/v1/summits/@SUMMIT_ID/schedule';

schedule_admin_view_api.getScheduleByDayAndLocation = function (summit_id, day, location_id)
{
    schedule_admin_view_api.trigger('beforeScheduleByDayAndLocationRetrieved',{});
    var url = api_base_url.replace('@SUMMIT_ID', summit_id)+'?day='+day+'&location='+location_id;
    return $.get(url,function (data) {
        schedule_admin_view_api.trigger('ScheduleByDayAndLocationRetrieved', data);
    });
}

module.exports = schedule_admin_view_api;