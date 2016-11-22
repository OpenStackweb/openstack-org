import nl2br from '../utils/nl2br';

export default (state, action) => {
	switch(action.type) {
		case 'VOTE_PRESENTATION':
			if(state.id === action.id) {
				return {
					...state,
					user_vote: action.vote
				}
			}

			return state;

		case 'COMMENT_PRESENTATION':
			if(state.id === action.id) {
				return {
					...state,
					user_comment: {
						id: +new Date(),
						author: 'You',
						ago: 'Just now',
						comment: nl2br(action.comment)
					}	
				}
			}

			return state;

		case 'RECEIVE_PRESENTATION':
			if(state.id === action.payload.id) {				
				return {
					...state,
					...action.payload
				};
			}

			return state;

		case 'REMOVE_USER_COMMENT':
			if(state.id === action.payload.id) {				
				return {
					...state,
					user_comment: null
				};
			}

			return state;

		default:
			return state;
	}
};