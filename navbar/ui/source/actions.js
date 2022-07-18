import {
    getRequest,
    createAction, startLoading, stopLoading, showMessage,
} from 'openstack-uicore-foundation/lib/methods';

export const RECEIVE_SPONSORED_PROJECTS = 'RECEIVE_SPONSORED_PROJECTS';
export const REQUEST_SPONSORED_PROJECTS = 'REQUEST_SPONSORED_PROJECTS';

export const errorHandler = (err, res) => (dispatch) => {
    let code = err.status;
    let msg = '';
    let error_message = {};

    switch (code) {
        case 404: {
            let messages = err.response.body.messages;
            for (var i in messages) {
                msg += '- ' + messages[i].message + '<br>';
            }

            error_message = {
                title: 'Not Found',
                html: msg,
                type: 'error'
            };

            dispatch(showMessage(error_message));
        }
            break;
        case 412: {
            let messages = err.response.body.messages;
            for (var i in messages) {
                msg += '- ' + messages[i].message + '<br>';
            }

            error_message = {
                title: 'Validation error',
                html: msg,
                type: 'error'
            };
            dispatch(showMessage(error_message));
        }
            break;
        default:
            error_message = {
                title: 'ERROR',
                html: 'Server Error',
                type: 'error'
            };
            dispatch(showMessage( error_message ));
    }
}

export const fetchAll = () => (dispatch) => {

    dispatch(startLoading());

    let params = {
        page: 1,
        per_page: 100,
        order: 'name',
    };

    const baseUrl = window.navBarConfig.baseApiUrl;

    return getRequest(
        null,
        createAction(RECEIVE_SPONSORED_PROJECTS),
        `${baseUrl}/api/public/v1/sponsored-projects`,
        errorHandler,
        params
    )({})(dispatch).then(() => {
            dispatch(stopLoading());
        }
    );
}


