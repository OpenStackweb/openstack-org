/**
 * Copyright 2020 Open Infrastructure Foundation
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

import { getRequest, putRequest, createAction } from "~core-utils/actions";

import URI from "urijs";

export const RECEIVE_PAGE = 'RECEIVE_PAGE';
export const REQUEST_PAGE = 'REQUEST_PAGE';
export const UPDATING = 'UPDATING';
export const UPDATED  = 'UPDATED';

export const fetchAll = getRequest(
    createAction(REQUEST_PAGE),
    createAction(RECEIVE_PAGE),
    'api/v1/marketplace/public-cloud-passports/public_clouds'
);

export const updateItem = (params) => dispatch => {
    putRequest(
        createAction(UPDATING),
        createAction(UPDATED),
        `api/v1/marketplace/public-cloud-passports/${params.item.id}`,
        params.item
    )(params)(dispatch);
}

/*export const exportAllProducts = (params) => dispatch => {

    let url = URI('api/v1/marketplace/openstack-powered-implementations/export/csv').query(params).toString();
    window.open(url);
}*/
