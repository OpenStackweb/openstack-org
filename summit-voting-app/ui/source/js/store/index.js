import { combineReducers, applyMiddleware, compose, createStore } from 'redux';
import { reduxReactRouter, routerStateReducer } from 'redux-router';
import { createHistory } from 'history';
import DevTools from '../components/containers/DevTools';
import thunk from 'redux-thunk';
import categories from '../reducers/categories';
import presentations from '../reducers/presentations';
import ui from '../reducers/ui';
import mobile from '../reducers/mobile';

const reducer = combineReducers({
	categories,
	presentations,
	ui,
	mobile
});

const composedCreateStore = compose(
  applyMiddleware(thunk),
  reduxReactRouter({createHistory}),
  DevTools.instrument()
)(createStore);

const Store = composedCreateStore(reducer);

export default Store;
