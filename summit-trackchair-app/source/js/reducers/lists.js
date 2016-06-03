/*eslint-disable */

export const lists = function (
    state = {
        results: null,        
        loading: false        
    },
    action = {}) {	

    switch(action.type) {
        case 'REQUEST_LISTS':
            return {
                ...state,
                loading: true
            };
        case 'RECEIVE_LISTS':
            return {
                ...state,
                results: [...action.payload.response.lists],                
                loading: false
            };

        case 'SORT_SELECTIONS':
        	return {
        		...state,
				results: state.results.map(list => {
					if(list.id == action.payload.listID) { 
						return {
							...list,
							selections: action.payload.selections.map((s,i) => (
								{...s, order: i+1}
							))
						}
					}
					return list;
				})
        	};

        default:
            return state;

    }
};
/*eslint-enable */
