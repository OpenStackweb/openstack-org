/*eslint-disable */
const map = (results) => {
	return results.map(data => {
		const r = [
			data.id,
			{title: data.presentation_title, id: data.presentation_id},
            {status: data.status, reason: data.reject_reason, approver: data.approver},
			data.old_category.title,
			data.new_category.title,
			data.requester
		];

		if(data.chair_of_new || data.is_admin) {
			r.push(data.has_selections ? 'has_selections' : data.id);
		} else {
            r.push('not_admin')
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
        			if(+r[6] === action.payload.requestID) {
        				const newRow = [...r];
        				newRow[2].status = action.payload.approved ? 'Approved' : 'Rejected';
        				newRow[2].reason = action.payload.rejectReason;
                        newRow[2].approver = window.TrackChairAppConfig.userinfo.name;
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
