/*eslint-disable */
export const presentations = function (
    state = {
        results: null,
        has_more: false,
        loading: false,
        page: 0,
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
                loading: false
            };

        case 'ACTIVATE_PRESENTATION_FILTER':
        	return {
        		...state,
        		filter: action.payload
        	};

        case 'MARK_AS_READ':
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
        default:
            return state;

    }
};
/*eslint-enable */
