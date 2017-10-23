import React from 'react';
import ReactDOM from 'react-dom';
import thunk from 'redux-thunk';
import { Provider } from 'react-redux';
import { createStore, applyMiddleware } from 'redux';
import Schedule from '../components/schedule';
import reducers from '../reducers/index';

const createStoreWithMiddleware = applyMiddleware(thunk)(createStore);

require("awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css");

const props = {
    ...window.ReactScheduleGridProps,
}

ReactDOM.render(
    <Provider store={createStoreWithMiddleware(reducers)}>
        <Schedule {...props} />
    </Provider>,
    document.getElementById('os-schedule-react')
);