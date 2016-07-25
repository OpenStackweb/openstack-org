/*eslint-disable */
import React from 'react';
import { render } from 'react-dom';
import { Provider } from 'react-redux';
import { browserHistory } from 'react-router';
import store from './store';
import Routes from './routes';
import './rx/routeActions';
import URL from './utils/url';
import paramsListener from './utils/paramsListener';

const { baseURL } = window.TrackChairAppConfig;
const routeChildren = Routes(baseURL);
URL.setBaseURL(baseURL);

browserHistory.listen(paramsListener(routeChildren));

document.addEventListener('DOMContentLoaded', function init() {
  if (document.getElementById('trackchair-app')) {
    render(
      <Provider store={store} children={routeChildren} />,
      document.getElementById('trackchair-app')
    );
  }
});
