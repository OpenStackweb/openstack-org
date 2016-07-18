/*eslint-disable */
export const main = function (
	state = {
		errorMsg: null,
		notification: null,
		mobileMenu: false
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
			default:
				return state;

		}
};
/*eslint-enable */
