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

        default:
            return state;

    }
};
/*eslint-enable */
