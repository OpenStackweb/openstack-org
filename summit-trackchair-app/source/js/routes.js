import React from 'react';
import { Router, Route, browserHistory, IndexRoute } from 'react-router';
import App from './components/pages/App';
import Browse from './components/pages/Browse';
import Selections from './components/pages/Selections';
import Directory from './components/pages/Directory';
import Help from './components/pages/Help';
import ChangeRequests from './components/pages/ChangeRequests';
import BrowseDetail from './components/pages/BrowseDetail';
import SelectionsDetail from './components/pages/SelectionsDetail';
import { syncHistoryWithStore } from 'react-router-redux';
import store from './store';

const history = syncHistoryWithStore(browserHistory, store);

const Routes = (baseURL) => (
    <Router history={history}>
      <Route path={baseURL} component={App}>
      	<IndexRoute component={Browse} />
      	<Route path="browse" component={Browse}>
        	<Route path=":id" component={BrowseDetail} />
        </Route>
        <Route path="selections" component={Selections}>
        	<Route path=":id" component={SelectionsDetail} />
        </Route>
        <Route path="directory" component={Directory} />
        <Route path="change-requests" component={ChangeRequests} />
        <Route path="help" component={Help} />
      </Route>
    </Router>
);

export default Routes;