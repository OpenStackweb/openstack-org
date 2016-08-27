/*eslint-disable */

import { combineReducers } from 'redux';
import { main } from './main';
import { videos } from './videos';
import { video } from './video';
import { videoDetail } from './videoDetail';
import { summits } from './summits';
import { speakers } from './speakers';
import { routeReducer } from 'react-router-redux';

export const reducers = combineReducers({
  main,
  videos,
  video,
  summits,
  speakers,
  videoDetail,
  router: routeReducer
});
/*eslint-enable */
