import React from 'react';
import { render } from 'react-dom';
import { Provider } from 'react-redux';
import { ReduxRouter  } from 'redux-router';
import { Route } from 'react-router';
import Store from './store';
import Config from './utils/Config';
import VotingApp from './components/views/VotingApp';
import PresentationDetail from './components/views/PresentationDetail';

Config.load(window.VotingAppConfig);

const routes = (
  <Route path={Config.get('baseURL').replace(/\/$/,'')} component={VotingApp}>
      <Route path="presentation/:id" component={PresentationDetail} />
  </Route>
);

render(
	<Provider store={Store}>
	<div>
		<ReduxRouter>
			{routes}
		</ReduxRouter>
	</div>
	</Provider>,
	document.getElementById('voting-app')		
);

