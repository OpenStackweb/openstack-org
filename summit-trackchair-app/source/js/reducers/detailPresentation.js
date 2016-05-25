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

        default:
            return state;

    }
};
/*eslint-enable */
