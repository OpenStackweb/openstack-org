export const allVideos = (
	state = { 
		results: [],
		loading: false,
		has_more: false,
		total: 0,
		summit: null,
		speaker: null
	}, 
	action = {}
) => {
	switch(action.type) {

		case 'REQUEST_ALL_VIDEOS':
			return {
				...state,
				loading: true
			};
		case 'RECEIVE_ALL_VIDEOS': {
			const {response} = action.payload;
			return {
				...state,
				results: state.has_more ? 
							[...state.results, ...response.results] : 
							[...response.results],
				loading: false,
				has_more: response.has_more,
				total: response.total

			}
		}
		case 'RECEIVE_LATEST_VIDEO': {
			const {response} = action.payload;
			if(!state.results.length) return state;

			if(!state.results.some(v => +v.id === +response.id)) {
				return {
					...state,
					results: [
						{
							...response,
							isNew: true
						},
						...state.results
					]
				};
			}

			return state;
		}
		case 'RECEIVE_VIDEO_DETAIL': {
			const {response} = action.payload;			
			return {
				...state,
				results: state.results.map(video => {
					if(video.isNew && +video.id === +response.id) {
						return {
							...video,
							isNew: false
						}
					}

					return video;
				})
			}
		}
		default:
			return state			
	}
};

export const summitVideos = (
	state = {
		summit: null,
		speaker: null,
		loading: false,
		results: [],
		has_more: false,
		total: 0
	}, 
	action = {}
) => {
	switch(action.type) {
		case 'REQUEST_SUMMIT_VIDEOS':
			if( (action.payload.summit && !state.summit) ||
				(state.summit && +state.summit.id !== +action.payload.summit)
			) {
				return {
					summit: null,
					speaker: null,
					results: [],
					loading: true,
					has_more: false,
					total: 0
				};
			}
			return {
				...state,
				loading: true
			};

		case 'RECEIVE_SUMMIT_VIDEOS':
			const {response} = action.payload;
			return {
				...state,
				summit: {
					...response.summit
				},
				results: state.has_more ? 
							[...state.results, ...response.results] :
							[...response.results],				
				loading: false,
				has_more: response.has_more,
				total: response.total
			};

		default:
			return state			

	}
}

export const tagVideos = (
    state = {
        tag: null,
        loading: false,
        results: [],
        has_more: false,
        total: 0
    },
    action = {}
) => {
    switch(action.type) {
        case 'REQUEST_TAG_VIDEOS':
            if( (action.payload.tag && !state.tag) ||
                (state.tag && +state.tag.tag !== +action.payload.tag)
                ) {
                return {
                    tag: null,
                    results: [],
                    loading: true,
                    has_more: false,
                    total: 0
                };
            }
            return {
                ...state,
                loading: true
            };

        case 'RECEIVE_TAG_VIDEOS':
            const {response} = action.payload;
            return {
                ...state,
                tag: {
                    ...response.tag
                },
                results: state.has_more ?
                    [...state.results, ...response.results] :
                    [...response.results],
                loading: false,
                    has_more: response.has_more,
                    total: response.total
            };

        default:
            return state

    }
}

export const trackVideos = (
    state = {
        track: null,
        loading: false,
        results: [],
        has_more: false,
        total: 0
    },
    action = {}
) => {
    switch(action.type) {
        case 'REQUEST_TRACK_VIDEOS':
            if( (action.payload.track && !state.track) ||
                (state.track && +state.track.slug !== +action.payload.track)
                ) {
                return {
                    track: null,
                    results: [],
                    loading: true,
                    has_more: false,
                    total: 0
                };
            }
            return {
                ...state,
                loading: true
            };

        case 'RECEIVE_TRACK_VIDEOS':
            const {response} = action.payload;
            return {
                ...state,
                track: {
                    ...response.track
                },
                results: state.has_more ?
                    [...state.results, ...response.results] :
                    [...response.results],
                loading: false,
                    has_more: response.has_more,
                    total: response.total
            };

        default:
            return state

    }
}

export const speakerVideos = (
	state = {
		speaker: null,
		summit: null,
		results: [],
		loading: false,
		has_more: false,
		total: 0
	},
	action = {}
) => {
	switch(action.type) {
		case 'REQUEST_SPEAKER_VIDEOS':
			if( (action.payload.speaker && !state.speaker) ||
				(state.speaker && +state.speaker.id !== +action.payload.speaker)
			) {
				return {
					speaker: null,
					summit: null,
					results: [],
					loading: true,
					has_more: false,
					total: 0
				};
			}
			return {
				...state,
				loading: true
			};

		case 'RECEIVE_SPEAKER_VIDEOS':
			const {response} = action.payload;
			return {
				...state,
				speaker: {
					...response.speaker
				},
				results: state.has_more ?
							[...state.results, ...response.results] :
							[...response.results],
				loading: false,
				has_more: response.has_more,
				total: response.total
			};

		default:
			return state			

	}
};

export const highlightedVideos = (
	state = {
		results: [],
		loading: false
	},
	action = {}
) => {
	switch(action.type) {
		case 'REQUEST_HIGHLIGHT_VIDEOS':
			return {
				...state,
				loading: true
			}

		case 'RECEIVE_HIGHLIGHT_VIDEOS':		
			return {
				results: [...action.payload.response.results],
				loading: false
			};

		default:
			return state;
	}
};	


export const popularVideos = (
	state = {
		results: [],
		loading: false
	},
	action = {}
) => {
	switch(action.type) {
		case 'REQUEST_POPULAR_VIDEOS':
			return {
				...state,
				loading: true
			}

		case 'RECEIVE_POPULAR_VIDEOS':
			return {
				results: [...action.payload.response.results],
				loading: false
			};

		default:
			return state;
	}
};	


export const searchVideos = (
	state = {
		results: null,
		loading: false,
		activeTab: 'titleMatches'
	},
	action = {}
) => {	
	switch(action.type) {
		case 'REQUEST_SEARCH_VIDEOS':		
			return {
				activeTab: state.activeTab,
				results: null,
				loading: true
			};

		case 'RECEIVE_SEARCH_VIDEOS':
			const {results} = action.payload.response;
			let tab = state.activeTab;

			// If the current tab is set to something that has no results, find the first tab that has some.
			if(!results[state.activeTab] || !results[state.activeTab].length) {				
				for (let k in results) {
					if(results[k] && results[k].length) {
						tab = k;
						break;
					}
				}
			}

			return {
				activeTab: tab,
				results: {
					...results	
				},
				loading: false
			};

		case 'UPDATE_SEARCH_TAB':
			return {
				...state,
				activeTab: action.payload
			}
		
		default:
			return state
	}
}