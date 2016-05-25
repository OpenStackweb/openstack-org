import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
import { browserHistory } from 'react-router';
import {reducers} from './reducers';

const createStoreWithMiddleware = applyMiddleware(
	thunk
)(createStore);
const store = createStoreWithMiddleware(reducers);

export default store;