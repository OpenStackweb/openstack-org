/*eslint-disable */

export const directory = function (
    state = {
        data: [],
        sortCol: 0,
        sortDir: 1,
        loading: false,
        searchTerm: '',
        showAddForm: false,
        chairEmail: '',
        chairCategory: null,
        formMessage: null,
        emailCheck: null,
        formLoading: false,
        memberSearchResults: []
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
					{
						chair_id: chairData.chair_id,
						category: chairData.category,
						name: `${chairData.first_name} ${chairData.last_name}`,
						email: chairData.email,
						category_id: chairData.category_id
					}
				)),
                loading: false
            };

        case 'SORT_DIRECTORY':
        	const {sortDir, sortCol} = action.payload;

        	return {
        		...state,
        		sortCol,
        		sortDir
        	}

        case 'SEARCH_DIRECTORY':
        	const term = action.payload;
        	return {
        		...state,
        		searchTerm: term
        	}
        case 'TOGGLE_ADD_CHAIR':
        	return {
        		...state,
        		showAddForm: !state.showAddForm,
		        chairEmail: '',
		        chairCategory: null,
		        formMessage: null,
		        emailCheck: null,
		        formLoading: false
        	};
        case 'UPDATE_ADD_CHAIR_EMAIL':
        	return {
        		...state,
        		chairEmail: action.payload,
        		emailCheck: null
        	};
        case 'UPDATE_ADD_CHAIR_CATEGORY':
        	return {
        		...state,
        		chairCategory: action.payload
        	};
        case 'UPDATE_EMAIL_CHECK':
        	return {
        		...state,
        		emailCheck: action.payload
        	};
        case 'TOGGLE_ADD_CHAIR_LOADING':
        	return {
        		...state,
        		formLoading: !state.formLoading
        	};
        case 'UPDATE_ADD_CHAIR_MESSAGE':
        	return {
        		...state,
        		formMessage: {
        			...action.payload
        		},
        		formLoading:false
        	};
        case 'ADD_NEW_CHAIR':
        	const {category, chair_id, category_id, first_name, last_name, email} = action.payload;
        	return {
        		...state,
        		data: [
					{
						chair_id,
						category,
						category_id,
						name: `${first_name} ${last_name}`,
						email
					},
					...state.data
        		],
        		formLoading: false
        	}

        case 'DELETE_CHAIR':
        	const {chairID, categoryID} = action.payload;
        	return {
        		...state,
        		data: state.data.filter(row => (
        			row.chair_id !== chairID || row.category_id !== categoryID
        		))
        	}

        case 'RECEIVE_MEMBER_SEARCH':
        	return {
        		...state,
        		memberSearchResults: [
        			...action.payload.response
        		]
        	}

        case 'CHOOSE_MEMBER_SEARCH_ITEM':
        	const chair = state.memberSearchResults.find(item => item.id === action.payload);
        	return {
        		...state,
        		chairEmail: chair ? chair.email : state.chairEmail,
        		emailCheck: !!chair,
        		memberSearchResults: []
        	}

        default:
            return state;

    }
};
/*eslint-enable */
