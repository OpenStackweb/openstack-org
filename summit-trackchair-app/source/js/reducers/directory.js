/*eslint-disable */
const sortedData = (data, sortCol, sortDir) => {
	return data.sort((aObj,bObj) => {
		let a = aObj[sortCol].toUpperCase();
		let b = bObj[sortCol].toUpperCase();
		let result = (a < b ? -1 : (a > b ? 1 : 0));
		return result*sortDir;
    })	
};

export const directory = function (
    state = {
        data: [],
        sortCol: 0,
        sortDir: 1,
        loading: false,
        searchTerm: '',
        searchResults: []
    },
    action = {}) {
    switch(action.type) {
        case 'REQUEST_SUMMIT':
            return {
                ...state,
                loading: true
            };
        case 'RECEIVE_SUMMIT':
            return {
                ...state,
                data: action.payload.response.chair_list.map(chairData => (
					[
						chairData.category, 
						`${chairData.first_name} ${chairData.last_name}`,
						chairData.email
					]
				)),
                loading: false
            };

        case 'SORT_DIRECTORY':
        	const {sortDir, sortCol} = action.payload;

        	return {
        		...state,
        		data: sortedData(state.data, sortCol, sortDir),
        		sortCol,
        		sortDir
        	}

        case 'SEARCH_DIRECTORY':
        	const term = action.payload;
        	const rxp = new RegExp(term,'i');
        	return {
        		...state,
        		searchResults: sortedData(state.data.filter(chairData => (
        				chairData[0].match(rxp) ||
        				chairData[1].match(rxp) ||
        				chairData[2].match(rxp)
        			)), 
        			state.sortCol, 
        			state.sortDir
        		),
        		searchTerm: term
        	}
        default:
            return state;

    }
};
/*eslint-enable */
