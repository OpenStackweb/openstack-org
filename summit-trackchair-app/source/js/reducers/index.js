/*eslint-disable */

import { combineReducers } from 'redux';
import { main } from './main';
import { summit } from './summit';
import { presentations } from './presentations';
import { detailPresentation } from './detailPresentation';
import { routerReducer } from 'react-router-redux';

export const reducers = combineReducers({
  main,
  summit,
  presentations,
  detailPresentation,
  routing: routerReducer
});
/*eslint-enable */
