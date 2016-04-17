import React from 'react';
import { Router, Route, browserHistory, IndexRoute } from 'react-router';
import URL from './utils/url';

import App from './components/pages/App';
import AllVideos from './components/pages/AllVideos';
import Summits from './components/pages/Summits';
import SummitDetail from './components/pages/SummitDetail';
import Speakers from './components/pages/Speakers';
import SpeakerDetail from './components/pages/SpeakerDetail';
import Featured from './components/pages/Featured';
import Search from './components/pages/Search';
import VideoDetail from './components/pages/VideoDetail';

const Routes = (baseURL) => (
    <Router history={browserHistory}>
      <Route path={baseURL} component={App}>
      	<IndexRoute component={AllVideos} />
        <Route path="summits" component={Summits}/>
        <Route path="summits/show/:id" component={SummitDetail}/>
        <Route path="speakers" component={Speakers} />
        <Route path="speakers/show/:id" component={SpeakerDetail} />
        <Route path="featured" component={Featured} />
        <Route path="search" component={Search} />
        <Route path="video/:slug" component={VideoDetail} />
      </Route>
    </Router>
);

export default Routes;