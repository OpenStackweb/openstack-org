import React from 'react';
import { render } from 'react-dom';
import { Provider } from 'react-redux';
import Store from './store';
import Config from './utils/Config';
import VotingApp from './components/views/VotingApp';
import { Router, Route, useRouterHistory } from 'react-router';
import { createHashHistory } from 'history';
// useRouterHistory creates a composable higher-order function
const appHistory = createHashHistory({ queryKey: false });
Config.load(window.VotingAppConfig);
Config.set('isMobile', window.innerWidth < 768);

render(
	<Provider store={Store}>
		<Router history={appHistory}>
			<Route path="/(:filter)" component={VotingApp}/>
		</Router>
	</Provider>,
	document.getElementById('voting-app')		
);

