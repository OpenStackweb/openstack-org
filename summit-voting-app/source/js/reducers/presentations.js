import presentation from './presentation';
require('array.prototype.findindex');
import nl2br from '../utils/nl2br';

export default (state = {
	presentations: [],
	selectedPresentation: {},
	total: null,
	initialised: false,
	search: null,
	category: null
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
				total: null
			};
		
		case 'CLEAR_PRESENTATION':
			return {
				...state,
				selectedPresentation: null
			};
		
		case 'SELECT_PRESENTATION':
			if(action.payload === state.selectedPresentation.id) {
				return state;
			}
			return {
				...state,
				selectedPresentation: {
					id: action.payload
				}
			};

		case 'RECEIVE_PRESENTATION':
			const newState = {
				...state,
				selectedPresentation: {...action.payload},
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
					...state.selectedPresentation,
					navigationDirection: action.payload
				}
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