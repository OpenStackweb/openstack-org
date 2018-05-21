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

const isObjectEmpty = (obj) => {
    return Object.keys(obj).length === 0 && obj.constructor === Object ;
}

export const getRequest =
(
    requestActionCreator,
    receiveActionCreator,
    endpoint,
    errorHandler
) => (params = {}) => dispatch => {

    console.log(`endpoint ${endpoint}`);

    let url = URI(endpoint);

    if(!isObjectEmpty(params))
        url = url.query(params);

    console.log(`url ${url.toString()}`);

    if(requestActionCreator){
        if(typeof requestActionCreator === 'function')
            dispatch(requestActionCreator(params));
        else
            dispatch(requestActionCreator);
    }

    const key = `${requestActionCreator().type}_${JSON.stringify(params || {})}`;
    cancel(key);

    return new Promise((resolve, reject) => {
        http.get(url.toString())
            .timeout({
                response: 60000,
                deadline: 60000,
            })
            .end(
                responseHandler(
                    dispatch,
                    json => {
                        if(receiveActionCreator){
                            if(typeof receiveActionCreator === 'function') {
                                dispatch(receiveActionCreator({
                                    response: json
                                }));
                            }
                            else
                                dispatch(receiveActionCreator);
                        }
                        return resolve();
                    },
                    errorHandler
                )
            )
    });
};

export const putRequest = (
    requestActionCreator,
    receiveActionCreator,
    endpoint,
    payload,
    errorHandler

) => (params = {}) => dispatch => {
    console.log(`endpoint ${endpoint}`);

    let url = URI(endpoint);

    if(!isObjectEmpty(params))
        url = url.query(params);

    console.log(`url ${url.toString()}`);

    if(requestActionCreator){
        if(typeof requestActionCreator === 'function')
            dispatch(requestActionCreator(params));
        else
            dispatch(requestActionCreator);
    }

    return new Promise((resolve, reject) => {
        http.put(url.toString())
            .timeout({
                response: 60000,
                deadline: 60000,
            })
            .send(payload)
            .end(
                responseHandler(
                    dispatch,
                    json => {
                        if(receiveActionCreator){
                            if(typeof receiveActionCreator === 'function') {
                                dispatch(receiveActionCreator({
                                    response: json
                                }));
                            }
                            else
                                dispatch(receiveActionCreator);
                        }
                        return resolve();
                    },
                    errorHandler
                )
            )
    });
};

export const deleteRequest = (
    requestActionCreator,
    receiveActionCreator,
    endpoint,
    payload,
    errorHandler
) => (params) => (dispatch) => {
    let url = URI(endpoint).toString();

    if(requestActionCreator){
        if(typeof requestActionCreator === 'function')
            dispatch(requestActionCreator(params));
        else
            dispatch(requestActionCreator);
    }

    return new Promise((resolve, reject) => {
        http.delete(url)
            .send(payload)
            .end(
                responseHandler(
                    dispatch,
                    json => {
                        if(receiveActionCreator){
                            if(typeof receiveActionCreator === 'function') {
                                dispatch(receiveActionCreator({
                                    response: json
                                }));
                            }
                            else
                                dispatch(receiveActionCreator);
                        }
                        return resolve();
                    },
                    errorHandler
                )
            )
    });
};

export const postRequest = (
    requestActionCreator,
    receiveActionCreator,
    endpoint,
    payload,
    errorHandler
) => (params = {}) => dispatch => {

    let url = URI(endpoint);

    if(!isObjectEmpty(params))
        url = url.query(params);

    console.log(`url ${url.toString()}`);

    if(requestActionCreator){
        if(typeof requestActionCreator === 'function')
            dispatch(requestActionCreator(params));
        else
            dispatch(requestActionCreator);
    }

    const req = http.post(url)
        .send(payload)
        .end(
            responseHandler(
                dispatch,
                json => {

                    if(receiveActionCreator){
                        if(typeof receiveActionCreator === 'function') {
                            dispatch(receiveActionCreator({
                                response: json
                            }));
                        }
                        else
                            dispatch(receiveActionCreator);
                    }

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
            }
            console.log(err, res);
            dispatch(showMessage({msg:GENERIC_ERROR, msg_type:'error'}));
        }
        else if(typeof success === 'function') {
            success(res.body);
        }
    };
};