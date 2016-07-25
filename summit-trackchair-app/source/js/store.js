import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
import { browserHistory } from 'react-router';
import {reducers} from './reducers';
import createLogger from 'redux-logger';

const createStoreWithMiddleware = applyMiddleware(
	thunk //,	createLogger()
)(createStore);
const store = createStoreWithMiddleware(reducers);

export default store;