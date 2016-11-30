const initialState = {
	loading: false,
	results: [],
	has_more: false,
	total: 0,
	letter: undefined
};

export const speakers = function (state, action = {}) {
	if(!state) {
		try {
			if(window && window.VideoAppConfig) {
				state = window.VideoAppConfig.initialState.speakers;
			}
		}
		catch (e) {
			state = {
				...initialState
			};
		}
	}

	switch(action.type) {
		case 'REQUEST_SPEAKERS':			
			if(action.payload.letter !== state.letter) {
				return {
					...initialState,
					loading: true,
					letter: action.payload.letter
				}
			}
			
			return {
				...state,
				loading: true
			};
		case 'RECEIVE_SPEAKERS':
			const {response} = action.payload;
			return {
				...state,
				results: state.has_more ?
							[...state.results, ...response.results] :
							[...response.results],
				has_more: response.has_more,
				total: response.total,
				loading: false
			};

		default:
			return state;
	}
};

