/*eslint-disable */
const map = (results) => {
	return results.map(data => (
		[
			data.presentation_title,
			data.done ? 'Completed' : 'Requested',
			data.old_category.title,
			data.new_category.title,
			data.requester
		]
	));
}
export const changeRequests = function (
    state = {
        results: null,
        has_more: false,
        loading: false,
        page: 1,
        sortCol: 'status',
        sortDir: -1,
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

        default:
            return state;

    }
};
/*eslint-enable */
