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
import TagDetail from './components/pages/TagDetail';
import TrackDetail from './components/pages/TrackDetail';

browserHistory.listenBefore(location => {
	if(location.search.match(/^\?search=/) || location.pathname.match(/video\/.*$/)) {
		const main = document.getElementById('video-navigation');
		const box = main.getBoundingClientRect();
		window.scrollTo(0, box.top - 50);
	}
});
const Routes = (baseURL) => (
    <Router history={browserHistory}>
      <Route path={baseURL} component={App}>
      	<IndexRoute component={AllVideos} />
        <Route path="summits" component={Summits}/>
        <Route path="summits/:slug" component={SummitDetail}/>
        <Route path="speakers" component={Speakers} />
        <Route path="speakers/:id/:slug" component={SpeakerDetail} />
        <Route path="featured" component={Featured} />
        <Route path="search" component={Search} />
        <Route path="tags/:tag" component={TagDetail}/>
        <Route path=":summit/tracks/:slug" component={TrackDetail}/>
        <Route path=":summit/:slug" component={VideoDetail} />
      </Route>
    </Router>
);

export default Routes;