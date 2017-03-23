import React from 'react'
import ReactDOM from 'react-dom'
import thunk from 'redux-thunk'
import { Provider } from 'react-redux'
import { createStore, applyMiddleware } from 'redux'

import Schedule from '../components/schedule'
import reducers from '../reducers/index'

const createStoreWithMiddleware = applyMiddleware(thunk)(createStore);

require("../../../../../themes/openstack/bower_assets/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css");

var schedule_api     = require('./schedule-api.js');
var schedule_filters = require('./schedule-filters.js');

require('./schedule-global-filter.tag')
riot.mount('schedule-global-filter');

const props = {
    schedule_api,
    schedule_filters,
    ...window.ReactScheduleGridProps,
}

ReactDOM.render(
    <Provider store={createStoreWithMiddleware(reducers)}>
        <Schedule {...props} />
    </Provider>,
    document.getElementById('os-schedule-react')
);