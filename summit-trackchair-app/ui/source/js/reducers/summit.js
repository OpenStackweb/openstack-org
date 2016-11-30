/*eslint-disable */
export const summit = function (
    state = {
        data: null,
        defaultCategory: null,
        loading: false
    },
    action = {}) {
    switch(action.type) {
        case 'REQUEST_SUMMIT':
            return {
                ...state,
                loading: true
            };
        case 'RECEIVE_SUMMIT':
        	const {response} = action.payload;
            return {
                ...state,
                data: {
                	...response
                },
                defaultCategory: response.categories.find(c => (
					c.user_is_chair
					)) || response.categories[0],
                loading: false
            };


        default:
            return state;

    }
};
/*eslint-enable */
