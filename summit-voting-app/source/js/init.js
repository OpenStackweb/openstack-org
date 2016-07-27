import React from 'react';
import { render } from 'react-dom';
import { Provider } from 'react-redux';
import Store from './store';
import Config from './utils/Config';
import VotingApp from './components/views/VotingApp';

Config.load(window.VotingAppConfig);

render(
	<Provider store={Store}>
		<VotingApp />
	</Provider>,
	document.getElementById('voting-app')		
);

