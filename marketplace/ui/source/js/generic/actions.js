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

const GENERIC_ERROR = "Yikes. Something seems to be broken. Our web team has been notified, and we apologize for the inconvenience.";

export const createAction = type => payload => ({
    type,
    payload
});


const xhrs = {};

const cancel = (key) => {
    if(xhrs[key]) {
        xhrs[key].abort();
        console.log(`aborted request ${key}`);
        delete xhrs[key];
    }
}

const schedule = (key, req) => {
    xhrs[key] = req;
};

const delay = (ms) => new Promise(resolve => setTimeout(resolve, ms));

export const getRequest =
(
    requestActionCreator,
    receiveActionCreator,
    endpoint
) => params => dispatch => {
    dispatch(requestActionCreator(params));
    const key = `${requestActionCreator().type}_${JSON.stringify(params || {})}`;
    console.log(`key ${key}`);
    cancel(key);
    let url = URI(endpoint).query(params).toString();
    console.log(`url is ${url}`);
    const req = http.get(url)
        .end(responseHandler(dispatch, json => {
            dispatch(receiveActionCreator({
                response: json
            }));
        }))
    schedule(key, req);
};

export const putRequest = (
    requestActionCreator,
    receiveActionCreator,
    endpoint,
    payload
) => params => dispatch => {
    let url = URI(endpoint).toString();
    dispatch(requestActionCreator(params));
    const req = http.put(url)
        .send(payload)
        .end(responseHandler(dispatch, json => {
            dispatch(receiveActionCreator({
                response: json
            }));
        }))

};

export const throwError = createAction('THROW_ERROR');

export const responseHandler = (dispatch, success, errorHandler) => {
    return (err, res) => {
        if (err || !res.ok) {
            if(errorHandler) {
                errorHandler(err, res);
            }
            else {
                console.log(err, res);
                dispatch(throwError(GENERIC_ERROR));
            }
        }
        else if(typeof success === 'function') {
            success(res.body);
        }
    };
};