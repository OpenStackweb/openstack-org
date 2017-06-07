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

export const RECEIVE_PRODUCTS_PAGE = 'RECEIVE_PRODUCTS_PAGE';
export const REQUEST_PRODUCTS_PAGE = 'REQUEST_PRODUCTS_PAGE';

export const PRODUCT_UPDATING = 'PRODUCT_UPDATING';
export const PRODUCT_UPDATED  = 'PRODUCT_UPDATED';

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

const getRequest =
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

const putRequest = (
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

export const fetchAllProducts = getRequest(
    createAction(REQUEST_PRODUCTS_PAGE),
    createAction(RECEIVE_PRODUCTS_PAGE),
    'api/v1/marketplace/openstack-powered-implementations'
);

export const updateProductField = (params, payload) => dispatch => {
    putRequest(
        createAction(PRODUCT_UPDATING),
        createAction(PRODUCT_UPDATED),
        `api/v1/marketplace/openstack-powered-implementations/${params.product_id}`,
        payload
    )(params)(dispatch);
}

export const exportAllProducts = (params) => dispatch => {

    let url = URI('api/v1/marketplace/openstack-powered-implementations/export/csv').query(params).toString();
    window.open(url);
}

export const navigateToProductDetails = (productId) => dispatch => {
    window.location = `/sangria/ViewPoweredOpenStackProductDetail/${productId}`;
}

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