/**
 * Copyright 2018 OpenStack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/

import {
    getRequest,
    postRequest,
    createAction,
    stopLoading,
    startLoading,
    showMessage
} from 'openstack-uicore-foundation/lib/methods';

export const REQUEST_ITEMS = 'REQUEST_ITEMS';
export const RECEIVE_ITEMS = 'RECEIVE_ITEMS';
export const RECEIVE_META  = 'RECEIVE_META';

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

export const fetchAll = (order, orderDir, page, releases) => (dispatch) => {

    let releaseIds = (releases.length > 0) ? releases.map(r => r.value).join(',') : null;

    let params = { order, orderDir, page, releaseIds };

    dispatch(startLoading());

    return getRequest(
        createAction(REQUEST_ITEMS),
        createAction(RECEIVE_ITEMS),
        'api/v1/software/contributors',
        errorHandler,
        { order, orderDir, page, releases }
    )(params)(dispatch).then(() => {
            dispatch(stopLoading());
        }
    );
}

export const fetchReleases = () => (dispatch) => {

    dispatch(startLoading());

    return getRequest(
        null,
        createAction(RECEIVE_META),
        'api/v1/software/releases',
        errorHandler
    )({})(dispatch).then(() => {
            dispatch(stopLoading());
        }
    );;
}

export const ingestContributors = (files) => (dispatch) => {

    dispatch(startLoading());

    postRequest(
        null,
        createAction(RECEIVE_ITEMS),
        `api/v1/software/contributors/ingest`,
        files,
        errorHandler
    )({})(dispatch)
        .then(() => {
            dispatch(stopLoading());
        });
}

export const exportContributors = (order, orderDir, releases) => (dispatch) => {
    let releaseIds = (releases.length > 0) ? releases.map(r => r.value).join(',') : null;

    window.open(`/sangria/ExportReleaseContributors?order=${order}&orderDir=${orderDir}&releaseIds=${releaseIds}`, '_blank');
}



