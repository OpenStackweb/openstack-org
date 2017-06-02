/*eslint-disable */

export const presentations = function (
    state = {
        results: null,
        has_more: false,
        loading: false,
        page: 0,
        total: 0,
        filter: 'all'
    },
    action = {}) {	
    switch(action.type) {
        case 'REQUEST_PRESENTATIONS':
            return {
                ...state,
                loading: true
            };
        case 'RECEIVE_PRESENTATIONS':
        	const {page} = action.payload.response;

            return {
                ...state,
                results: +page === 1 ? 
                	action.payload.response.results :
                	[...state.results, ...action.payload.response.results],
                has_more: action.payload.response.has_more,
                page: action.payload.response.page,
                total: action.payload.response.total,
                loading: false
            };

        case 'CLEAR_PRESENTATIONS':
    		return {
    			results: null,
    			has_more: false,
    			loading:true,
    			page: 0,
    			total: 0,
    			filter: 'all'
    		};        	
        case 'ACTIVATE_PRESENTATION_FILTER':
        	return {
        		...state,
        		filter: action.payload
        	};

        case 'TOGGLE_FOR_ME':
        	const metrics = {
        		selected: 'selectors',
        		maybe: 'likers',
        		pass: 'passers'
        	};
        	return {
        		...state,
        		results: state.results.map(p => {
        			if(+p.id === +action.payload.presentationID) {
        				let subtractFrom = metrics[p.selected];
        				let addTo = metrics[action.payload.type];        				
        				let newPresentation = {
        					...p,
        					selected: action.payload.type,
        					[addTo]: [...p[addTo], action.payload.name]
        				};
        				if(subtractFrom) {
        					newPresentation[subtractFrom] = newPresentation[subtractFrom].filter(name => (
        						name !== action.payload.name
        					))
        				}

        				return newPresentation;
        			}

        			return p;
        		})
        	}

        case 'MARK_AS_READ':
       		if(state.results) {
	        	return {
	        		...state,
	        		results: state.results.map(p => {
	        				if(+p.id === +action.payload) {
	        					return {
	        						...p,
	        						viewed: true
	        					};
	        				}

	        				return p;
	        			})
	        	};
        	}

        	return state;
        	
        default:
            return state;

    }
};
/*eslint-enable */
