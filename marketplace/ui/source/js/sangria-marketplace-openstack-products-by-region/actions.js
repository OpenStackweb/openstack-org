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
import URI from "urijs";
import { getRequest, putRequest, createAction } from "~core-utils/actions";

export const RECEIVE_PRODUCTS_PAGE = 'RECEIVE_PRODUCTS_PAGE';
export const REQUEST_PRODUCTS_PAGE = 'REQUEST_PRODUCTS_PAGE';

export const fetchPage = getRequest(
    createAction(REQUEST_PRODUCTS_PAGE),
    createAction(RECEIVE_PRODUCTS_PAGE),
    'api/v1/sangria/marketplace/regional-services'
);

export const exportAll = (params) => dispatch => {

    let url = URI('api/v1/sangria/marketplace/regional-services/export/csv').query(params).toString();
    window.open(url);
}
