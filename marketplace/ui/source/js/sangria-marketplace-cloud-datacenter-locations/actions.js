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
import URI from "urijs";
import { getRequest, createAction, defaultErrorHandler } from "~core-utils/actions";

export const RECEIVE_CLOUDS_PAGE = 'RECEIVE_CLOUDS_PAGE';
export const REQUEST_CLOUDS_PAGE = 'REQUEST_CLOUDS_PAGE';

export const fetchPage = (params) => {
        return dispatch => {
            return getRequest(
                createAction(REQUEST_CLOUDS_PAGE),
                createAction(RECEIVE_CLOUDS_PAGE),
                'api/v1/sangria/marketplace/cloud-services',
                defaultErrorHandler
            )(params)(dispatch);
        }
}

export const exportAll = (params) => dispatch => {
    let url = URI('api/v1/sangria/marketplace/cloud-services/export/csv').query(params).toString();
    window.open(url);
}
