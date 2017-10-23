/**
 * Copyright 2017 OpenStack Foundation
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
import request from 'superagent';
import URI from "urijs";
let http = request;
import 'sweetalert2/dist/sweetalert2.css';
import swal from 'sweetalert2';

export const defaultErrorHandler = (err, res) => {
    let text = res.body;
    if(res.body != null && res.body.messages instanceof Array) {
        let messages = res.body.messages.map( m => {
            if (m instanceof Object) return m.message
            else return m;
        })
        text = messages.join('\n');
    }
    swal(res.statusText, text, "error");
}

const GENERIC_ERROR        = "Yikes. Something seems to be broken. Our web team has been notified, and we apologize for the inconvenience.";
export const CLEAR_MESSAGE = 'CLEAR_MESSAGE';
export const SHOW_MESSAGE  = 'SHOW_MESSAGE';
export const STOP_LOADING  = 'STOP_LOADING';

export const createAction = type => payload => ({
    type,
    payload
});

export const clearMessage = createAction(CLEAR_MESSAGE);
export const showMessage  = createAction(SHOW_MESSAGE);
export const stopLoading  = createAction(STOP_LOADING);

const xhrs = {};

const cancel = (key) => {
    if(xhrs[key]) {
        xhrs[key].abort();
        console.log(`aborted request ${key}`);
        delete xhrs[key];
    }
}

const schedule = (key, req) => {
    console.log(`scheduling ${key}`);
    xhrs[key] = req;
};


const delay = (ms) => new Promise(resolve => setTimeout(resolve, ms));

export const getRequest =
(
    requestActionCreator,
    receiveActionCreator,
    endpoint,
    errorHandler
) => params => dispatch => {
    dispatch(requestActionCreator(params));
    const key = `${requestActionCreator().type}_${JSON.stringify(params || {})}`;
    cancel(key);
    let url = URI(endpoint).query(params).toString();
    const req = http.get(url)
        .timeout({
            response: 60000,
            deadline: 60000,
        })
        .end(
            responseHandler(
                dispatch,
                json => {
                    dispatch(receiveActionCreator({
                        response: json
                    }));
                },
                errorHandler
            )
        )
    schedule(key, req);
};

export const putRequest = (
    requestActionCreator,
    receiveActionCreator,
    endpoint,
    payload,
    errorHandler
) => params => dispatch => {
    console.log(`endpoint ${endpoint}`);
    let url = URI(endpoint).query(params).toString();
    console.log(`url ${url}`);
    dispatch(requestActionCreator(params));
    const req = http.put(url)
        .timeout({
            response: 60000,
            deadline: 60000,
        })
        .send(payload)
        .end(
            responseHandler(
                dispatch,
                json => {
                    dispatch(receiveActionCreator({
                        response: json
                    }));
                },
                errorHandler
            )
        )
};

export const deleteRequest = (
    requestActionCreator,
    receiveActionCreator,
    endpoint,
    payload,
    errorHandler
) => params => dispatch => {
    let url = URI(endpoint).toString();
    dispatch(requestActionCreator(params));
    const req = http.delete(url)
        .send(payload)
        .end(
            responseHandler(
                dispatch,
                json => {
                    dispatch(receiveActionCreator({
                        response: json
                    }));
                },
                errorHandler
            )
        )
};

export const postRequest = (
    requestActionCreator,
    receiveActionCreator,
    endpoint,
    payload,
    errorHandler
) => params => dispatch => {
    let url = URI(endpoint).query(params).toString();
    dispatch(requestActionCreator(params));
    const req = http.post(url)
        .send(payload)
        .end(
            responseHandler(
                dispatch,
                json => {
                    dispatch(receiveActionCreator({
                        response: json
                    }));
                },
                errorHandler
            )
        )

};

export const responseHandler = (dispatch, success, errorHandler) => {
    return (err, res) => {
        dispatch(stopLoading());
        if (err || !res.ok) {
            if(errorHandler) {
                errorHandler(err, res);
                return;
            }
            console.log(err, res);
            dispatch(showMessage({msg:GENERIC_ERROR, msg_type:'error'}));
        }
        else if(typeof success === 'function') {
            success(res.body);
        }
    };
};