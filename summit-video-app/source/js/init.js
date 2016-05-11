require('babel/polyfill');

/*eslint-disable */
import React from 'react';
import { render } from 'react-dom';
import { Provider } from 'react-redux';
import store from './store';
import Routes from './routes';
import URL from './utils/url';

const { baseURL } = window.VideoAppConfig;
URL.setBaseURL(baseURL);

/*eslint-enable */


document.addEventListener('DOMContentLoaded', function init() {
  if (document.getElementById('video-app')) {
    render(
      <Provider store={store} children={Routes(baseURL)} />,
      document.getElementById('video-app')
    );
  }
});
