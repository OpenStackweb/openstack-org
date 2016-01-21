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

		case 'RECEIVE_PRESENTATION':
			if(state.id === action.payload.id) {				
				return {
					...state,
					...action.payload
				};
			}

		default:
			return state;
	}
};