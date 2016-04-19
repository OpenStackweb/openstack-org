export const videoDetail = function (state, action = {}) {
	if(!state) {
		try {
			if(window && window.VideoAppConfig) {
				state = window.VideoAppConfig.initialState.videoDetail;
			}
		}
		catch (e) {
			state = {
				video: null,
				loading: false
			}
		}
	}
	switch(action.type) {
		case 'REQUEST_VIDEO_DETAIL':
			if(state.video && state.video.slug === action.payload) {
				return state;
			}
			
			return {
				video: null,
				loading: true
			};
			
		case 'RECEIVE_VIDEO_DETAIL':
			return {
				video: {
					...action.payload.response
				},
				loading: false
			};

		default:
			return state;
	}
};

