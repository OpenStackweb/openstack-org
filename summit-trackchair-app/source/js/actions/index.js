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
export const requestPresentations = createAction('REQUEST_PRESENTATIONS');
export const receivePresentations = createAction('RECEIVE_PRESENTATIONS');
export const requestPresentationDetail = createAction('REQUEST_PRESENTATION_DETAIL');
export const receivePresentationDetail = createAction('RECEIVE_PRESENTATION_DETAIL');
export const createComment = createAction('CREATE_COMMENT');
export const syncComment = createAction('SYNC_COMMENT');
export const toggleForMe = createAction('TOGGLE_FOR_ME');
export const toggleForGroup = createAction('TOGGLE_FOR_GROUP');
export const requestLists = createAction('REQUEST_LISTS');
export const receiveLists = createAction('RECEIVE_LISTS');
export const reorganiseSelections = createAction('REORGANISE_SELECTIONS');
export const sortDirectory = createAction('SORT_DIRECTORY');
export const searchDirectory = createAction('SEARCH_DIRECTORY');
export const requestChangeRequests = createAction('REQUEST_CHANGE_REQUESTS');
export const receiveChangeRequests = createAction('RECEIVE_CHANGE_REQUESTS');
export const sortChangeRequests = createAction('SORT_CHANGE_REQUESTS');
export const searchChangeRequests = createAction('SEARCH_CHANGE_REQUESTS');
export const activatePresentationFilter = createAction('ACTIVATE_PRESENTATION_FILTER');
export const markAsRead = createAction('MARK_AS_READ');
export const beginEmail = createAction('BEGIN_EMAIL');
export const successEmail = createAction('SUCCESS_EMAIL');
export const toggleRequestCategoryChange = createAction('TOGGLE_REQUEST_CATEGORY_CHANGE');
export const toggleEmailSpeakers = createAction('TOGGLE_EMAIL_SPEAKERS');
export const toggleMaybeDrawer = createAction('TOGGLE_MAYBE_DRAWER');
export const requestCategoryChange = createAction('REQUEST_CATEGORY_CHANGE');
export const successCategoryChange = createAction('SUCCESS_CATEGORY_CHANGE');
export const resolveRequest = createAction('RESOLVE_REQUEST');

/* Async Actions */

export const fetchSummit = (id) => {
	return createRequestReceiveAction(
    	requestSummit,
    	receiveSummit,
    	`summit/${id}`
	)(id);
}


export const fetchPresentations = createRequestReceiveAction(
    requestPresentations,
    receivePresentations,
    ''
);

export const fetchChangeRequests = createRequestReceiveAction(
    requestChangeRequests,
    receiveChangeRequests,
    'changerequests'
);

export const fetchLists = (category) => {
	return createRequestReceiveAction(
		requestLists,
		receiveLists,
		`selections/${category}`
	)(category);
};


export const fetchPresentationDetail = (id) => {
	return createRequestReceiveAction(
    	requestPresentationDetail,
    	receivePresentationDetail,
    	`presentation/${id}`
	)(id);
};

export const postMySelection = (presentationID, type, name) => {
	return (dispatch) => {
		const key = `TOGGLE_FOR_ME_${presentationID}`;
		dispatch(toggleForMe({presentationID, type, name}));
		cancel(key);

		const url = URL.create(
			`presentation/${presentationID}/select`,
			{type},
			'/trackchairs/api/v1'
		);

		const req = http.put(url)
			.end(responseHandler(dispatch, json => {
			}));
		schedule(key, req);
	}
};

export const postGroupSelection = (presentationID, bool) => {
	return (dispatch) => {
		const key = `TOGGLE_FOR_GROUP_${presentationID}`;
		dispatch(toggleForGroup(bool));
		cancel(key);

		const url = URL.create(
			`presentation/${presentationID}/group/${bool ? 'select' : 'unselect'}`,
			{},
			'/trackchairs/api/v1'
		);

		const req = http.put(url)
			.end(responseHandler(dispatch, json => {
			}));
		schedule(key, req);
	}
};


export const postComment = (presentationID, commentData) => {
	return (dispatch) => {
		
		const key = `POST_COMMENT__${JSON.stringify(commentData || {})}`;
		const __id = +new Date();
		dispatch(createComment({
			...commentData,
			__id
		}));
		cancel(key);
		
		const url = URL.create(
			`presentation/${presentationID}/comment`,
			{}, 
			'/trackchairs/api/v1'
		);
		
		const req = http.post(url)
			.send({comment: commentData.body})
			.type('form')
			.end(responseHandler(dispatch, json => {
				dispatch(syncComment({
					__id,
					response: json
				}));
			}));
		schedule(key, req);
	};
}

export const postEmail = (presentationID, emailData) => {
	return (dispatch) => {
		
		const key = `POST_EMAIL__${JSON.stringify(emailData || {})}`;
		const __id = +new Date();
		cancel(key);
		dispatch(beginEmail());
		const url = URL.create(
			`presentation/${presentationID}/emailspeakers`,
			{}, 
			'/trackchairs/api/v1'
		);
		
		const req = http.post(url)
			.send({email: emailData.body})
			.type('form')
			.end(responseHandler(dispatch, json => {
				dispatch(successEmail(true));
				setTimeout(() => successEmail(false), 3000);
			}));
		schedule(key, req);
	};
}

export const postReorganise = (listID, collection, newOrder) => {
	return (dispatch, getState) => {
		const key = `REORDER_${listID}_${collection}`;
		dispatch(reorganiseSelections({listID, collection, newOrder}));
		cancel(key);
		const collectionMap = {
			selections: 'selected',
			maybes: 'maybe',
			team: 'selected'
		};
		const data = {
			list_id: listID,
			order: newOrder.map(s => s.id),
			collection: collectionMap[collection]
		};
		const req = http.put('/trackchairs/api/v1/reorder')
			.send(data)
			.end(responseHandler(dispatch));
		schedule(key, req);
	};
};

export const postResolveRequest = (requestID, approved) => {
	return (dispatch, getState) => {
		const key = `RESOLVE_${requestID}`;
		dispatch(resolveRequest({requestID, approved}));
		cancel(key);

		const req = http.put(`/trackchairs/api/v1/categorychange/resolve/${requestID}`)
			.send({approved})
			.end(responseHandler(dispatch));

		schedule(key, req);
	}
};

export const postCategoryChange = (presentationID, newCategory) => {
	return (dispatch, getState) => {
		const key = `REQUEST_CATEGORY_CHANGE_${presentationID}`;
		dispatch(requestCategoryChange({presentationID, newCategory}));
		cancel(key);
		
		const data = {
			new_cat: newCategory
		};
		const url = URL.create(
			`presentation/${presentationID}/categorychange/new`,
			{}, 
			'/trackchairs/api/v1'
		);

		const req = http.post(url)
			.send(data)
			.type('form')
			.end(responseHandler(dispatch, json => {
				dispatch(successCategoryChange(true));
				//setTimeout(() => successCategoryChange(false), 3000);
			}));

		
		schedule(key, req);
	}
}

export const postMarkAsRead = (presentationID) => {
	return (dispatch) => {
		const key = `MARK_AS_READ_${presentationID}`;
		dispatch(markAsRead(presentationID));
		cancel(key);

		const url = URL.create(
			`presentation/${presentationID}/markasviewed`,
			{},
			'/trackchairs/api/v1'
		);

		const req = http.put(url)
			.end(responseHandler(dispatch, json => {
			}));
		schedule(key, req);
	}
};


/*eslint-enable */