/*eslint-disable */
export const summit = function (
    state = {
        data: null,
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
            return {
                ...state,
                data: {
                	...action.payload.response
                },
                loading: false
            };


        default:
            return state;

    }
};
/*eslint-enable */
