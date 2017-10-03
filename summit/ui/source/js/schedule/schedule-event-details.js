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
import React from 'react';
import ReactDOM from 'react-dom';
import thunk from 'redux-thunk';
import reduceReducers from 'reduce-reducers';
import { Provider } from 'react-redux';
import { createStore, applyMiddleware, compose } from 'redux';
import ActionButtons from '../components/schedule-event-details/action-buttons';
import { scheduleEventDetailsReducer } from '../reducers/schedule-event-details';
import { genericReducers }  from "~core-utils/reducers";
import { ShareButtons } from '../components/schedule-event-details/share-buttons';
import EventComments from  '../components/schedule-event-details/event-comments';

const composeEnhancers = window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ || compose;

let reducer = reduceReducers(scheduleEventDetailsReducer, genericReducers);
const store = createStore(reducer,{
    event: event,
    currentUser: current_user
}, composeEnhancers(applyMiddleware(thunk)));

ReactDOM.render(
    <Provider store={store}>
        <ActionButtons/>
    </Provider>,
    document.getElementById('action-buttons-container')
);

ReactDOM.render(
    <ShareButtons share_info={share_info}/>,
    document.getElementById('share-buttons-container')
);

ReactDOM.render(
    <Provider store={store}>
        <EventComments limit={5}/>
    </Provider>,
    document.getElementById('comments-container')
);