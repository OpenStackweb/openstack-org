import presentation from './presentation';
require('array.prototype.findindex');
import nl2br from '../utils/nl2br';

export default (state = {
	presentations: [],
	selectedPresentation: {},
	requestedPresentationID: null,
	total: null,
	initialised: false,
	search: null,
	category: null,
	navigationDirection: 'down'
}, action) => {
	switch(action.type) {	
		case 'RECEIVE_PRESENTATIONS':
			return {
				...state,
				...action.payload,
				initialised: true,
				presentations: state.presentations.concat(action.payload.presentations)
			};
		
		case 'CLEAR_PRESENTATIONS':
			return {
				...state,
				presentations: [],
				selectedPresentation: state.selectedPresentation,
				requestedPresentationID: null,
				total: null
			};
		
		case 'CLEAR_PRESENTATION':
			return {
				...state,
				selectedPresentation: null,
				requestedPresentationID: null
			};
		
		case 'SELECT_PRESENTATION':
			if(action.payload === state.requestedPresentationID) {
				return state;
			}

			const currentIndex = state.presentations.findIndex(p => p.id === state.selectedPresentation.id);
			const targetIndex = state.presentations.findIndex(p => p.id === action.payload);
			const navigationDirection = targetIndex > currentIndex ? 'down' : 'up';

			return {
				...state,
				requestedPresentationID: action.payload,
				navigationDirection
			};

		case 'RECEIVE_PRESENTATION':
			const newState = {
				...state,
				selectedPresentation: {...action.payload},
				requestedPresentationID: action.payload.id,
				presentations: state.presentations.map(p => presentation(p, action))
			};

			return newState;

		case 'VOTE_PRESENTATION':
			return {
				...state,
				selectedPresentation: {
					...state.selectedPresentation,
					user_vote: action.vote
				},
				presentations: state.presentations.map(p => presentation(p, action))
			};

		case 'TOGGLE_COMMENT_FORM':
			return {
				...state,
				selectedPresentation: {
					...state.selectedPresentation,
					showForm: action.payload
				}
			};

		case 'COMMENT_PRESENTATION':		
			return {
				...state,
				selectedPresentation: {
					...state.selectedPresentation,
					user_comment: {
						id: +new Date(),
						comment: nl2br(action.comment),
						author: 'You',
						ago: 'Just now'
					},
					showForm: false
				},
				presentations: state.presentations.map(p => presentation(p, action))				
			};
		
		case 'REMOVE_USER_COMMENT':
			return {
				...state,
				selectedPresentation: {
					...state.selectedPresentation,
					user_comment: null
				}
			};

		case 'NAVIGATE_ADJACENT':
			return {
				...state,
				selectedPresentation: {
					...state.selectedPresentation
				},
				navigationDirection: action.payload > 0 ? 'down' : 'up'
			};
		case 'UPDATE_CATEGORY':
			return {
				...state,
				category: action.payload
			};

		case 'UPDATE_SEARCH':
			return {
				...state,
				search: action.payload
			};
		default:
			return state;
	}
};