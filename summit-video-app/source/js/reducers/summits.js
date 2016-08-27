export const summits = function (state, action = {}) {
	if(!state) {
		try {
			if(window && window.VideoAppConfig) {
				state = window.VideoAppConfig.initialState.summits;
			}
		}
		catch (e) {
			state = {
				loading: false,
				results: []
			};
		}
	}

	switch(action.type) {
		case 'REQUEST_SUMMITS':
			return {
				...state,
				loading: true
			}
		case 'RECEIVE_SUMMITS':
			return {
				...action.payload.response,
				loading: false
			};

		default:
			return state;
	}
};

