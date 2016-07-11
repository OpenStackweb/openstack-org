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
        searchResults: [],
        showAddForm: false,
        chairEmail: '',
        chairCategory: null,
        formMessage: null,
        emailCheck: null,
        formLoading: false
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
        		chairEmail: action.payload
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
        	const {category, first_name, last_name, email} = action.payload;
        	return {
        		...state,
        		data: [
					[
						category,
						`${first_name} ${last_name}`,
						email
					],
					...state.data
        		],
        		formLoading: false
        	}

        default:
            return state;

    }
};
/*eslint-enable */
