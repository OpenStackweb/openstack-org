import presentation from './presentation';
require('array.prototype.findindex');

export default (state = {
	presentations: [],
	selectedPresentation: null,
	total: null,
	initialised: false
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
		
		case 'RECEIVE_PRESENTATION':
			const newState = {
				...state,
				selectedPresentation: action.payload,
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

		default:
			return state;
	}
};