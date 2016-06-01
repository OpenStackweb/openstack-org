import http from 'superagent';
import Config from '../utils/Config';
import url  from '../utils/url';
import { pushState } from 'redux-router';

require('array.prototype.findindex');
require('array.prototype.find');

const GENERIC_ERROR = "There seems to have been a problem. Please contact support@openstack.org for assitance.";

const xhrs = {};

const cancelPending = (xhrs, key) => {
	if(xhrs[key]) {
		xhrs[key].abort();
		delete xhrs[key];
	}
}

const api = (path, query) => {
	return url(['api', path], query);
};

const responseHandler = (dispatch, success, errorHandler) => {
    return (err, res) => {
        if (err || !res.ok) {
        	if(errorHandler) {
				errorHandler(err, res);
        	}
        	else {
				dispatch(throwError(GENERIC_ERROR));
        	}
        }
        else if(typeof success === 'function') {
        	dispatch(endXHR());
        	success(res.body);
        }
    };	
};

export function throwError (errorMsg) {
	return {
		type: 'THROW_ERROR',
		payload: errorMsg
	};
};

export function httpError (code) {
	return {
		type: 'HTTP_ERROR',
		payload: code
	};
};

export function clearError () {
	return {
		type: 'CLEAR_ERROR'
	}
};

export function beginXHR () {
	return {
		type: 'BEGIN_XHR'
	};
};

export function endXHR () {
	return {
		type: 'END_XHR'
	};
};

export function votePresentation (id, vote) {
	return {
		type: 'VOTE_PRESENTATION',
		id,
		vote
	};
};

export function commentPresentation (id, comment) {
	return {
		type: 'COMMENT_PRESENTATION',
		id,
		comment
	};
};

export function removeUserComment (id) {
	return {
		type: 'REMOVE_USER_COMMENT',
		payload: {
			id
		}
	};
};

export function toggleCommentForm (bool) {
	return {
		type: 'TOGGLE_COMMENT_FORM',
		payload: bool
	}
};

export function requestPresentations (params = {}) {
	return dispatch => {
		dispatch(beginXHR());
		cancelPending(xhrs, 'REQUEST_PRESENTATIONS');		
		let req = http.get(api('presentations.json', params))
				   .end(responseHandler(dispatch, json => {
				   		if(typeof json === 'object') {
				   			dispatch(receivePresentations(json, params));	
				   		}
				   		else {
				   			dispatch(throwError(GENERIC_ERROR))
				   		}
				   		
				   }));
				   
		return xhrs['REQUEST_PRESENTATIONS'] = req;
	}
};

export function requestCategories () {
	return dispatch => {
		dispatch(beginXHR());
		cancelPending(xhrs, 'REQUEST_CATEGORIES');		

		let req = http.get(api('categories.json'))
			.end(responseHandler(dispatch, json => {
				if(Array.isArray(json)) {
					dispatch(receiveCategories(json));
				}
				else {
					dispatch(throwError(GENERIC_ERROR));
				}
			}));

		return xhrs['REQUEST_CATEGORIES'] = req;
	}
};

export function requestPresentation (id) {
	return (dispatch, getState) => {
		const existing = getState().presentations.presentations.find(p => p.id == id);
		
		if(existing) {
			dispatch(receivePresentation(existing));
			if(existing.abstract) return;
		}
		dispatch(beginXHR());
		cancelPending(xhrs, 'REQUEST_PRESENTATION');

		let req = http.get(api(`presentation/${id}.json`))
					.end(responseHandler(
						dispatch, 
						json => {
							if(json && json.id) {
								dispatch(receivePresentation(json));
							}
							else {
								dispatch(throwError(GENERIC_ERROR));
							}
						}, 
						(err, res) => {
							dispatch(receivePresentation({
								error: true
							}));
						}
					));

		return xhrs['REQUEST_PRESENTATION'] = req;
	}
};

export function receivePresentations (json, params={}) {
	return {
		type: 'RECEIVE_PRESENTATIONS',
		payload: json,
		params
	}
};

export function receivePresentation (json) {	
	return {
		type: 'RECEIVE_PRESENTATION',
		payload: json
	}
};

export function clearPresentations () {
	return {
		type: 'CLEAR_PRESENTATIONS'
	}
};

export function receiveCategories (json) {
	return {
		type: 'RECEIVE_CATEGORIES',
		payload: json
	};
};

export function requestVote (id, vote) {
	return (dispatch, getState) => {
		const p = getState().presentations.presentations.find(p => p.id === id);
		const originalVote = p ? p.user_vote : null;

		dispatch(votePresentation(id, vote));
		cancelPending(xhrs, 'VOTE_PRESENTATION');
		
		let req = http.post(api(`presentation/${id}.json`))
				.send({vote})
				.end(responseHandler(
					dispatch, 
					null,
					(err, res) => {
						dispatch(throwError(GENERIC_ERROR));
						dispatch(votePresentation(id, originalVote));
					}
				));

		return xhrs['VOTE_PRESENTATION'] = req;
	}
}

export function postComment (id, comment) {
	return (dispatch, getState) => {
		const p = getState().presentations.presentations.find(p => p.id === id);
		const originalcomment = p ? p.user_comment : null;

		dispatch(commentPresentation(id, comment));
		cancelPending(xhrs, 'COMMENT_PRESENTATION');
		
		let req = http.post(api(`presentation/${id}.json`))
				.send({comment})
				.end(responseHandler(
					dispatch, 
					null,
					(err, res) => {
						dispatch(throwError(GENERIC_ERROR));
						dispatch(commentPresentation(id, originalComment));
					}
				));

		return xhrs['COMMENT_PRESENTATION'] = req;
	}
}

export function destroyUserComment (id) {
	return (dispatch, getState) => {
		const p = getState().presentations.presentations.find(p => p.id === id);

		dispatch(removeUserComment(id));
		cancelPending(xhrs, 'REMOVE_USER_COMMENT');
		
		let req = http.del(api(`presentation/${id}.json`))
				.end(responseHandler(
					dispatch, 
					null,
					(err, res) => {
						dispatch(throwError(GENERIC_ERROR));						
					}
				));

		return xhrs['REMOVE_USER_COMMENT'] = req;
	}
}

export function goToPresentation (id, adder) {
	return (dispatch, getState) => {
//		dispatch(clearPresentation())
		dispatch(
			pushState(
				null, 
				url(
					`presentation/${id}`,
					getState().router.location.query
				)
			)
		)
	}
};

export function navigatePresentations (adder) {	
	return (dispatch, getState) => {		
		const state = getState();
		const {presentations} = state;
		if(!presentations.selectedPresentation) return;
		
		let index = presentations.presentations.findIndex(p => p.id === presentations.selectedPresentation.id);
		if(index > -1) {
			index += adder;
			if(index < 0 || index > (presentations.presentations.length-1)) {			
				return;
			}
			dispatch(adjacentPresentation(adder));
			dispatch(
				goToPresentation(
					presentations.presentations[index].id,
					adder
				)
			);
		}
	};
};

export function adjacentPresentation(adder) {
	return {
		type: 'NAVIGATE_ADJACENT',
		payload: adder
	};
};

export function goToCategory (category) {
	return pushState(null,	url(
		null,
		{category}
	));
};			


export function setSearchTerm (term) {
	return pushState(null,	url(
		null,
		{q: term}
	));
};

export function clearPresentation () {
	return {
		type: 'CLEAR_PRESENTATION'
	};
}