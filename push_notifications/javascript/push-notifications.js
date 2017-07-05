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


messaging.onTokenRefresh(function() {
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


getRegistrationToken();



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

function subscribeTokenToTopic(token, topic) {
    fetch('https://iid.googleapis.com/iid/v1/'+token+'/rel/topics/'+topic, {
        method: 'POST',
        headers: new Headers({
            'Authorization': 'key='+apiKey
        })
    }).then(response => {
        if (response.status < 200 || response.status >= 400) {
            throw 'Error subscribing to topic: '+response.status + ' - ' + response.text();
        }
        console.log('Subscribed to "'+topic+'"');
    }).catch(error => {
        console.error(error);
    })
}

function getRegistrationToken() {
    messaging.getToken()
        .then(function(currentToken) {
            if (currentToken) {
                subscribeTokenToTopic(currentToken, topic);
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



