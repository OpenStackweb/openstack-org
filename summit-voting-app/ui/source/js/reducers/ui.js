export default (
	state = {
		loading: false,
		errorMsg: null
	}, 
	action
) => {
	switch(action.type) {
		case 'BEGIN_XHR':		
			return {
				...state,
				loading: true
			};

		case 'END_XHR':
			return {
				...state,
				loading: false
			};

		case 'THROW_ERROR':
			return {
				...state,
				errorMsg: action.payload
			};
		case 'HTTP_ERROR':
			return {
				...state,
				httpError: action.payload
			};
		case 'CLEAR_ERROR':
			return {
				...state,
				errorMsg: null
			};
			
		default: 
			return state;
	}
}