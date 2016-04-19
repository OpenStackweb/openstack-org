import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
import { syncHistory } from 'react-router-redux';
import { browserHistory } from 'react-router';
import {reducers} from './reducers';

const reduxRouterMiddleware = syncHistory(browserHistory)
const createStoreWithMiddleware = applyMiddleware(
	thunk,
	reduxRouterMiddleware
)(createStore);
const store = createStoreWithMiddleware(reducers);

export default store;