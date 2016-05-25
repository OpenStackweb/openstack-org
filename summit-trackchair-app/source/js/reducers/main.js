/*eslint-disable */
export const main = function (
	state = {
		errorMsg: null,
		notification: null
	}, 
	action = {}) {
		switch(action.type) {
			case 'THROW_ERROR':
				return {
					...state,
					errorMsg: action.payload
				};
			case 'CLEAR_ERROR':
				return {
					...state,
					errorMsg: null
				};

			case 'SEND_NOTIFICATION':
				return {
					...state,
					notification: {
						...action.payload
					}
				};

			case 'CLEAR_NOTIFICATION':
				return {
					...state,
					notification: null
				}
				
			default:
				return state;

		}
};
/*eslint-enable */
