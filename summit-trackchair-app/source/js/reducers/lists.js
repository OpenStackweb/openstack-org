/*eslint-disable */

const reorderSelections = (selections, selectionID, adder) => {	
	const currentIndex = selections.findIndex(s => s.id == selectionID);
	let newIndex = currentIndex+adder;
	if(newIndex < 0 || newIndex >= (selections.length-1)) {
		newIndex = currentIndex;
	}
	return selections.move(
		currentIndex, newIndex
	).map((s,i) => (
		{...s, order: i+1}
	));
};

const handleMove = (state, action, adder) => {
	return {
		...state,
		results: state.results.map(list => {
			if(list.id == action.payload.listID) {   
				return {
					...list,
					selections: reorderSelections(
						[...list.selections], 
						action.payload.selectionID,
						adder
					)
				}
			}
			return list;
		})
	};

}
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

        case 'MOVE_SELECTION_UP':
        	return handleMove(state, action, -1);
        	
        case 'MOVE_SELECTION_DOWN':
        	return handleMove(state, action, 1);

        default:
            return state;

    }
};
/*eslint-enable */
