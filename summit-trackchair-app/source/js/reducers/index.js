/*eslint-disable */

import { combineReducers } from 'redux';
import { main } from './main';
import { summit } from './summit';
import { presentations } from './presentations';
import { detailPresentation } from './detailPresentation';
import { lists } from './lists';
import { routerReducer } from 'react-router-redux';

export const reducers = combineReducers({
  main,
  summit,
  presentations,
  lists,
  detailPresentation,
  routing: routerReducer
});
/*eslint-enable */
