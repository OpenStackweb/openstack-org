/*eslint-disable */
import { responseHandler, cancel, schedule } from '../utils/ajax';
import URL from '../utils/url';
import request from 'superagent';

let http = request;

export function setHTTPClient (client) {
	http = client;
}

const createAction = type => payload => ({
	type,
	payload
});

const createRequestReceiveAction = (
	requestActionCreator, 
	receiveActionCreator, 
	endpoint
) => (params) => (dispatch) => {
	const key = `${requestActionCreator().type}_${JSON.stringify(params || {})}`;
	dispatch(requestActionCreator(params));
	cancel(key);
	const url = URL.create(endpoint, params);
	const req = http.get(url)
		.end(responseHandler(dispatch, json => {
			dispatch(receiveActionCreator({
				response: json
			}));
		}))
	schedule(key, req);
};

export const throwError = createAction('THROW_ERROR');

export const clearError = createAction('CLEAR_ERROR');

export const updateSearchText = createAction('UPDATE_SEARCH_TEXT');

export const updateSearchTab = createAction('UPDATE_SEARCH_TAB');

export const requestAllVideos = createAction('REQUEST_ALL_VIDEOS');

export const receiveAllVideos = createAction('RECEIVE_ALL_VIDEOS');

export const sendNotification = createAction('SEND_NOTIFICATION');

export const clearNotification = createAction('CLEAR_NOTIFICATION');

export const fetchAllVideos = createRequestReceiveAction(
	requestAllVideos,
	receiveAllVideos,
	'api/videos'
);

export const requestLatestVideo = createAction('REQUEST_LATEST_VIDEO');

export const receiveLatestVideo = createAction('RECEIVE_LATEST_VIDEO');

export const fetchLatestVideo = createRequestReceiveAction(	
	requestLatestVideo,
	receiveLatestVideo,
	'api/video/latest'
);

export const requestFeaturedVideo = createAction('REQUEST_FEATURED_VIDEO');

export const receiveFeaturedVideo = createAction('RECEIVE_FEATURED_VIDEO');

export const fetchFeaturedVideo = createRequestReceiveAction(
	requestFeaturedVideo,
	receiveFeaturedVideo,
	'api/video/featured'
);

export const requestHighlightVideos = createAction('REQUEST_HIGHLIGHT_VIDEOS');

export const receiveHighlightVideos = createAction('RECEIVE_HIGHLIGHT_VIDEOS');

export const fetchHighlightVideos = () => {
	return createRequestReceiveAction(
		requestHighlightVideos,
		receiveHighlightVideos,
		'api/videos'
	)({ highlighted: true });
};

export const requestPopularVideos = createAction('REQUEST_POPULAR_VIDEOS');

export const receivePopularVideos = createAction('RECEIVE_POPULAR_VIDEOS');

export const fetchPopularVideos = () => {
	return createRequestReceiveAction(
		requestPopularVideos,
		receivePopularVideos,
		'api/videos'
	)({ popular: true });
};

export const requestSpeakerVideos = createAction('REQUEST_SPEAKER_VIDEOS');

export const receiveSpeakerVideos = createAction('RECEIVE_SPEAKER_VIDEOS');

export const fetchSpeakerVideos = (speaker, start = 0) => {
	return createRequestReceiveAction(
		requestSpeakerVideos,
		receiveSpeakerVideos,
		'api/videos'
	)({speaker, start});
};

export const requestSummitVideos = createAction('REQUEST_SUMMIT_VIDEOS');

export const receiveSummitVideos = createAction('RECEIVE_SUMMIT_VIDEOS');

export const fetchSummitVideos = (summit, start = 0) => {
	return createRequestReceiveAction(
		requestSummitVideos,
		receiveSummitVideos,
		'api/videos'
	)({ summit, start });	
};	

export const requestSearchVideos = createAction('REQUEST_SEARCH_VIDEOS');

export const receiveSearchVideos = createAction('RECEIVE_SEARCH_VIDEOS');

export const fetchSearchVideos = (search) => {
	return createRequestReceiveAction(
		requestSearchVideos,
		receiveSearchVideos,
		'api/videos'
	)({ search });
};

export const requestVideoDetail = createAction('REQUEST_VIDEO_DETAIL');

export const receiveVideoDetail = createAction('RECEIVE_VIDEO_DETAIL');

export const fetchVideoDetail = (id) => {
	return createRequestReceiveAction(
		requestVideoDetail,
		receiveVideoDetail,
		`api/video/${id}`
	)(id);
};

export const requestSummits = createAction('REQUEST_SUMMITS');

export const receiveSummits = createAction('RECEIVE_SUMMITS');

export const fetchSummits = createRequestReceiveAction(
	requestSummits,
	receiveSummits,
	'api/summits'
);

export const requestSpeakers = createAction('REQUEST_SPEAKERS');

export const receiveSpeakers = createAction('RECEIVE_SPEAKERS');

export const fetchSpeakers = createRequestReceiveAction(
	requestSpeakers,
	receiveSpeakers,
	'api/speakers'
);

export const viewVideo = (videoID, token) => (dispatch) => {
	const key = `VIEW_VIDEO_${videoID}`;	
	cancel(key);

	const url = URL.create(`api/view/${videoID}`);
	const req = http.put(url)
		.end(responseHandler(dispatch, json => {
			dispatch(createAction('VIEW_VIDEO_SUCCESS', json));
		}))
	schedule(key, req);	
}


/*eslint-enable */