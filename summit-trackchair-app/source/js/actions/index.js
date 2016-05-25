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
	console.log('ugh');
	const key = `${requestActionCreator().type}_${JSON.stringify(params || {})}`;
	dispatch(requestActionCreator(params));
	cancel(key);
	const url = URL.create(endpoint, params, '/trackchairs/api/v1');
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

export const requestSummit = createAction('REQUEST_SUMMIT');

export const receiveSummit = createAction('RECEIVE_SUMMIT');

export const fetchSummit = (id) => {
	return createRequestReceiveAction(
    	requestSummit,
    	receiveSummit,
    	`summit/${id}`
	)(id);
}

export const requestPresentations = createAction('REQUEST_PRESENTATIONS');

export const receivePresentations = createAction('RECEIVE_PRESENTATIONS');

export const fetchPresentations = createRequestReceiveAction(
    requestPresentations,
    receivePresentations,
    ''
);

export const requestPresentationDetail = createAction('REQUEST_PRESENTATION_DETAIL');

export const receivePresentationDetail = createAction('RECEIVE_PRESENTATION_DETAIL');

export const fetchPresentationDetail = (id) => {
	return createRequestReceiveAction(
    	requestPresentationDetail,
    	receivePresentationDetail,
    	`presentation/${id}`
	)(id);
}

/*eslint-enable */