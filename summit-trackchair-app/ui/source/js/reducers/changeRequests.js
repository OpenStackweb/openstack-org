/*eslint-disable */
const map = (results) => {
	return results.map(data => {
		const r = [
			{title: data.presentation_title, id: data.presentation_id},
			data.status,
			data.old_category.title,
			data.new_category.title,
			data.requester		
		];

		if(data.is_admin) {
			r.push(data.has_selections ? false : data.id);
		}

		return r;
	});
}
export const changeRequests = function (
    state = {
        results: null,
        has_more: false,
        loading: false,
        page: 1,
        sortCol: 'status',
        sortDir: 1,
        search: null
    },
    action = {}) {	
    switch(action.type) {
        case 'REQUEST_CHANGE_REQUESTS':
            return {
                ...state,
                loading: true
            };
        case 'RECEIVE_CHANGE_REQUESTS':
        	const {page} = action.payload.response;

            return {
                ...state,
                results: +page === 1 ? 
                	map(action.payload.response.results) :
                	[...state.results, ...map(action.payload.response.results)],
                has_more: action.payload.response.has_more,
                page: +action.payload.response.page,
                loading: false
            };

        case 'SORT_CHANGE_REQUESTS':
        	return {
        		...state,
        		sortDir: action.payload.sortDir,
        		sortCol: action.payload.sortCol,
        		page: +action.payload.page
        	};

        case 'SEARCH_CHANGE_REQUESTS':
        	return {
        		...state,
        		search: action.payload
        	};

        case 'RESOLVE_REQUEST':
        	return {
        		...state,
        		results: state.results.map(r => {        			
        			if(+r[5] === action.payload.requestID) {        				
        				const newRow = [...r];
        				newRow[1] = action.payload.approved ? 'Approved' : 'Rejected';

        				return newRow;
        			}

        			return r;
        		})
        	};

        default:
            return state;

    }
};
/*eslint-enable */
