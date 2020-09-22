import React from 'react';
import ReactDOM from 'react-dom';
import FullScheduleView from '../components/schedule/full-view';
import thunk from 'redux-thunk';
import { Provider } from 'react-redux';
import { createStore, applyMiddleware } from 'redux';
import reducers from '../reducers/index';
const createStoreWithMiddleware = applyMiddleware(thunk)(createStore);
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
        events: events,
        filtered: [],
        loading: false,
    }
});

ReactDOM.render(
    <Provider store={ store }>
        <FullScheduleView
            currentView="day"
            base_url={base_url}
            isLoggedUser={is_logged_user}
            should_show_venues={should_show_venues}
            summitId={summit_id}
            backUrl={ backUrl }
            pdfUrl={pdfUrl}
            goBack={goBack}
        />
    </Provider>,
    document.getElementById('full-schedule-view-container')
);