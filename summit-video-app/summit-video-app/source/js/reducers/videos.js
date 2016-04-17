import { 
	allVideos, 
	summitVideos, 
	speakerVideos, 
	highlightedVideos,
	popularVideos,
	searchVideos 
} from './childVideoReducers';

export const videos = function (state, action = {}) {
	if(!state) {
		try {
			if(window && window.VideoAppConfig) {
				state = window.VideoAppConfig.initialState.videos;
			}
		}
		catch (e) {
			state = {
				allVideos: allVideos(),
				summitVideos: summitVideos(),
				speakerVideos: speakerVideos(),
				highlightedVideos: highlightedVideos(),
				popularVideos: popularVideos(),
				searchVideos: searchVideos()
			};
		}
	}

	return {
		...state,
		allVideos: allVideos(state.allVideos, action),
		summitVideos: summitVideos(state.summitVideos, action),
		speakerVideos: speakerVideos(state.speakerVideos, action),
		highlightedVideos: highlightedVideos(state.highlightedVideos, action),
		popularVideos: popularVideos(state.popularVideos, action),
		searchVideos: searchVideos(state.searchVideos, action)	
	};
};

