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
import { Provider } from 'react-redux';
import { createStore, applyMiddleware } from 'redux';
import reducers from '../reducers/index';
import ScheduleEventList from '../components/schedule/event-list'

const createStoreWithMiddleware = applyMiddleware(thunk)(createStore);

require("awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css");

const props = {
    ...window.ReactScheduleGridProps,
}

const store = createStoreWithMiddleware(reducers, {
    // set initial statue of the store, with search results
    schedule:  {
        view: {
            type: '',
            value: '',
        },
        filters: {
            values: {},
            visible: true,
            expanded: false,
            allowedTracks: [],
            calSync: false,
        },
        bulk: [],
        events: props.events,
        filtered: [],
        loading: false,
    }
});

import {
    ScheduleProps
} from '../actions';

// this is set here bc there is a global dep on this properties set on
// summit/ui/source/js/components/schedule.js line 20 (ScheduleGrid::constructor)
ScheduleProps.month = props.ScheduleProps.month;
ScheduleProps.summit = props.ScheduleProps.summit;
ScheduleProps.base_url = props.ScheduleProps.base_url;
ScheduleProps.search_url = props.ScheduleProps.search_url;

class SearchResultEventList extends React.Component {

    loadFacebookSdk(appId) {
        window.fbAsyncInit = function() {
            FB.init({
                appId: appId,
                xfbml: true,
                status: true,
                version : 'v2.12'
            });
        };

        (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    }

    componentDidMount() {
        const { summit } = this.props.ScheduleProps;

        if ( ! ('ontouchstart' in window)) {
            $('[data-toggle="tooltip"]').tooltip();
        }

        this.loadFacebookSdk(summit.share_info.fb_app_id)
    }

    render(){
        let props = this.props;
        return(<ScheduleEventList {...props} />);
    }
}

ReactDOM.render(
    <Provider store={ store }>
        <SearchResultEventList {...props}></SearchResultEventList>
    </Provider>,
    document.getElementById('search-result-event-list')
);
