/*eslint-disable */

import { combineReducers } from 'redux';
import { main } from './main';
import { summit } from './summit';
import { presentations } from './presentations';
import { detailPresentation } from './detailPresentation';
import { lists } from './lists';
import { routerReducer } from 'react-router-redux';
import { directory } from './directory';
import { changeRequests } from './changeRequests';

export const reducers = combineReducers({
  main,
  summit,
  presentations,
  lists,
  detailPresentation,
  directory,
  changeRequests,
  routing: routerReducer
});
/*eslint-enable */
