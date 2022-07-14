/*eslint-disable */
import React from 'react';
import { render } from 'react-dom';
import { Provider } from 'react-redux';
const containerId = 'nav_container';
import store from './store';
import App from './app';

document.addEventListener('DOMContentLoaded', function init() {
    if (document.getElementById(containerId)) {
        render(
            <Provider store={store}>
                <App />
            </Provider>,
            document.getElementById(containerId)
        );
    }
});