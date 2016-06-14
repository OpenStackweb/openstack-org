/*eslint-disable */
export const detailPresentation = function (
    state = {
    },
    action = {}) {	
    switch(action.type) {
        case 'REQUEST_PRESENTATION_DETAIL':
            return {
                ...state,
                loading: true
            };
        case 'RECEIVE_PRESENTATION_DETAIL':
            return {
                ...action.payload.response,
                loading: false
            };

        case 'CREATE_COMMENT':
        	return {
        		...state,
        		comments: [
        			...state.comments,
        			{
        				name: action.payload.name,
        				body: action.payload.body,
        				ago: 'Just now',
        				id: action.payload.__id
        			}
        		]
        	};

        case 'SYNC_COMMENT':
        	if(!action.payload.__id) {
        		throw new Error('Payload for SYNC_COMMENT must have an __id');
        	}
        	return {
        		...state,
        		comments: state.comments.map(c => {
        			return c.id === action.payload.__id ? {...action.payload.response} : c;
        		})
        	}

        case 'TOGGLE_FOR_ME':
        	return {
        		...state,
        		selected: !!action.payload
        	};

        case 'TOGGLE_FOR_GROUP':
        	return {
        		...state,
        		group_selected: !!action.payload
        	};

        case 'MARK_AS_READ':
        	return {
        		...state,
        		viewed: true
        	};
        	
        default:
            return state;

    }
};
/*eslint-enable */
