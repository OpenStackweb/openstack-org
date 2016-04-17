export const video = function (state, action = {}) {
	if(!state) {
		try {
			if(window && window.VideoAppConfig) {
				state = window.VideoAppConfig.initialState.video;
			}
		}
		catch (e) {
			state = {
				featuredVideo: {},
				latestVideo: {}
			};
		}
	}

	switch(action.type) {
		case 'REQUEST_LATEST_VIDEO':		
			return {
				...state,
				latestVideo: {
					...state.latestVideo,
					loading: true
				}
			};
		case 'RECEIVE_LATEST_VIDEO':
			return {
				...state,
				latestVideo: {
					...action.payload.response,
					loading: false
				}
			};
		case 'REQUEST_FEATURED_VIDEO':
			return {
				...state,
				featuredVideo: {
					...state.featuredVideo,
					loading: true
				}
			}
		case 'RECEIVE_FEATURED_VIDEO':
			return {
				...state,
				featuredVideo: {
					...action.payload.response,
					loading: false
				}
			};

		default:
			return state;
	}
};

