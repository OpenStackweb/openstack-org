/*eslint-disable */
export const main = function (
	state = {
		errorMsg: null,
		notification: null,
		mobileMenu: false,
		params: {}
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
			case 'TOGGLE_MOBILE_MENU':
				return {
					...state,
					mobileMenu: action.payload === undefined ? !state.mobileMenu : action.payload
				};
			case 'ROUTER_PARAMS_CHANGE':
				return {
					...state,
					params: {...action.payload.params}
				};
			default:
				return state;

		}
};
/*eslint-enable */
