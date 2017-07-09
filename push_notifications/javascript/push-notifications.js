/**
 * Copyright 2017 OpenStack Foundation
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

// init firebase
firebase.initializeApp(config);

// Retrieve Firebase Messaging object.
const messaging = firebase.messaging();

navigator.serviceWorker.register('/push_notifications/javascript/firebase-messaging-sw.js')
    .then((registration) => {
        messaging.useServiceWorker(registration);
        console.log('Service worker registered. Retrieving Token...');
        getRegistrationToken();
});


messaging.onTokenRefresh(function() {
    console.log('Token refreshed. Retrieving new token...');
    getRegistrationToken();
});

// Handle incoming messages. Called when:
// - a message is received while the app has focus
// - the user clicks on an app notification created by a sevice worker
//   `messaging.setBackgroundMessageHandler` handler.
messaging.onMessage(function(payload) {
    console.log("Message received. ", payload);
    // ...
});



function requestPermission() {
    messaging.requestPermission()
        .then(function() {
            console.log('Notification permission granted.');
            getRegistrationToken();
        })
        .catch(function(err) {
            console.log('Unable to get permission to notify.', err);
        });
}

function subscribeTokenToTopic(token) {
    $.ajax({
        type: 'POST',
        url: '/api/v1/push_notifications/subscribe/'+token+'/'+topic_channel
    }).done(function(response) {
        console.log('Subscribed to "'+topic_channel+'"');
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error('Error subscribing to topic: '+textStatus + ' - ' + errorThrown);
    });
}

function getRegistrationToken() {
    messaging.getToken()
        .then(function(currentToken) {
            if (currentToken) {
                //console.log('Token retrieved: '+currentToken);
                subscribeTokenToTopic(currentToken);
            } else {
                // Show permission request.
                console.log('No Instance ID token available. Request permission to generate one.');
                requestPermission();
            }
        })
        .catch(function(err) {
            console.log('An error occurred while retrieving token. ', err);
        });
}



