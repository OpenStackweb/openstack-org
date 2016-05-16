
var calendar_synch = riot.observable();
var schedule_api   = require('./schedule-api.js');

calendar_synch.addEventToGoogleCalendar = function(summit_id, event)
{
    var cal_event = {
        'summary': event.title,
        'location': event.location,
        'description': event.abstract,
        'start': {
            'dateTime': event.start_datetime.replace(' ','T'),
            'timeZone': 'America/Chicago'
        },
        'end': {
            'dateTime': event.end_datetime.replace(' ','T'), //'2016-05-15T14:00:00-07:00'
            'timeZone': 'America/Chicago'
        },
        'reminders': {
            'useDefault': false,
            'overrides': [
                {'method': 'email', 'minutes': 24 * 60},
                {'method': 'popup', 'minutes': 10}
            ]
        }
    };

    var request = gapi.client.calendar.events.insert({
        'calendarId': 'primary',
        'resource': cal_event
    });

    request.execute(function(cal_event) {
        calendar_synch.trigger('googleSynchComplete', summit_id, event.id, cal_event.id);
    });
}

calendar_synch.removeEventFromGoogleCalendar = function(summit_id, event_id, cal_event_id)
{
    var request = gapi.client.calendar.events.delete({
        'calendarId': 'primary',
        'eventId': cal_event_id
    });

    request.execute(function() {
        calendar_synch.trigger('googleUnSynchComplete', summit_id, event_id);
    });
}

module.exports = calendar_synch;