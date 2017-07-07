import React from 'react'
import ReactDOM from 'react-dom'
import thunk from 'redux-thunk'
import { Provider } from 'react-redux'
import { createStore, applyMiddleware } from 'redux'

import SyncCal from '../components/sync-calendar'
import reducers from '../reducers/index'

const createStoreWithMiddleware = applyMiddleware(thunk)(createStore);

const props = {
    ...window.ReactScheduleGridProps,
}

ReactDOM.render(
    <Provider store={createStoreWithMiddleware(reducers)}>
        <SyncCal {...props} />
    </Provider>,
    document.getElementById('os-schedule-sync-calendar')
);