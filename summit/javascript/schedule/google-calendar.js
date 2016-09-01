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


var GoogleCalendarApi = (function () {
    var my           = {};
    var authCallback = null;

    // Your Client ID can be retrieved from your project in the Google
    // Developer Console, https://console.developers.google.com
    var scopes       = ["https://www.googleapis.com/auth/calendar"];

    function handleAuthResult(authResult) {
        if (authResult && !authResult.error) {
            GoogleCalendarApi.authorized = true;
            console.log('handleAuthResult OK');
            loadCalendarApi();
            return;
        }
        GoogleCalendarApi.authorized = false;
        // Show auth UI, allowing the user to initiate authorization by
        // clicking authorize button.
        console.log('Google API error on Auth.');
    }

    function loadCalendarApi() {
        gapi.client.load('calendar', 'v3');
        if(authCallback != null){
            authCallback();
        }
        console.log('google calendar api v3 loaded!');
    }

    my.client_id    = null;
    my.authorized   = false;

    my.isAuthorized = function(){
        return this.authorized;
    };

    my.setClientId = function(client_id){
        this.client_id = client_id;
    };

    my.checkAuth = function () {
        console.log('GoogleCalendarApi.checkAuth');
        gapi.auth.authorize(
            {
                'client_id': this.client_id,
                'scope': scopes.join(' '),
                'immediate': true
            }, handleAuthResult);
    };

    my.doUserAuth = function(callback){
        authCallback = callback;
        swal({
            title: "Google Calendar Auth",
            text: "Authorize access to Google Calendar API ?",
            type: "warning",
            showCancelButton: true,
            showCloseButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Authorize",
        }).then(function() {
            gapi.auth.authorize(
                {client_id: this.client_id, scope: scopes.join(' '), immediate: false},
                handleAuthResult);
            return false;
        });
    }

    my.addEvents = function (events, callback){
        for(var i=0; i < events.length ; i++) {
            this.addEvent(events[i], callback);
        }
    };

    my.addEvent = function(event, callback){

        if(!this.authorized){
            console.log('you must be authorized to add events!');
            return;
        }

        if(event.gcal_id != '' && event.gcal_id != null) return;

        setTimeout(function(){
            var cal_event = {
                'summary': event.title,
                'location': event.location,
                'description': $(event.abstract).text(),
                'start': {
                    'dateTime': event.start_datetime.replace(' ', 'T'),
                    'timeZone': event.time_zone_id
                },
                'end': {
                    'dateTime': event.end_datetime.replace(' ', 'T'), //'2016-05-15T14:00:00-07:00'
                    'timeZone': event.time_zone_id
                },
                'reminders': {
                    'useDefault': false,
                    'overrides': [
                        {'method': 'email', 'minutes': 24 * 60},
                        {'method': 'popup', 'minutes': 10}
                    ]
                }
            };

            gapi.client.calendar.events.insert({
                'calendarId': 'primary',
                'resource': cal_event
            }).then(function(response) {
                if(callback != null){
                    callback(response, event);
                }
            }, function(reason) {
                console.log('Error: ' + reason.result.error.message);
            });
        }, 500);
    };

    my.removeEvents = function(events, callback){

        for(var i=0; i < events.length ; i++) {
            this.removeEvent(events[i], callback);
        }
    };

    my.removeEvent = function(event, callback){

        if(!this.authorized){
            console.log('you must be authorized to add events!');
            return;
        }
        setTimeout(function(){
            gapi.client.calendar.events.delete({
                'calendarId': 'primary',
                'eventId': event.gcal_id
            }).then(function (response) {
                if (callback != null) {
                    callback(response, event);
                }
            }, function (reason) {
                console.log('Error: ' + reason.result.error.message);
            });
        }, 500);
    };

    return my;
}());

function checkAuth(){
    GoogleCalendarApi.checkAuth();
}
