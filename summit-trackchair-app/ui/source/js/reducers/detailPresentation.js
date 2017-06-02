/*eslint-disable */
export const detailPresentation = function (
    state = {
    },
    action = {}) {	
    switch(action.type) {
    	case 'CLEAR_PRESENTATIONS':
    			return {};

        case 'REQUEST_PRESENTATION_DETAIL':
            return {
                ...state,
                loading: true,
                sending: false,
                emailSuccess: false,
                showForm: false,
                showChangeRequest: false,
                categorySuccess: false,
                requesting: false
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
        		selected: action.payload.type
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
        case 'BEGIN_EMAIL':
        	return {
        		...state,
        		sending: true
        	};

        case 'SUCCESS_EMAIL':
        	return {
        		...state,
        		emailSuccess: action.payload,
        		sending: false
        	};
        case 'TOGGLE_REQUEST_CATEGORY_CHANGE':
        	return {
        		...state,
        		showChangeRequest: !state.showChangeRequest
        	};
        case 'TOGGLE_EMAIL_SPEAKERS':
        	return {
        		...state,
        		showForm: !state.showForm
        	};
        case 'REQUEST_CATEGORY_CHANGE':
        	return {
        		...state,
        		requesting: true
        	};
        case 'SUCCESS_CATEGORY_CHANGE':
        	return {
        		...state,
        		requesting: false,
        		categorySuccess: action.payload,
        		change_requests_count: +state.change_requests_count+1
        	};
        default:
            return state;

    }
};
/*eslint-enable */
