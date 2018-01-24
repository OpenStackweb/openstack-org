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
import { getRequest, putRequest, createAction, deleteRequest } from "~core-utils/actions";

import URI from "urijs";

export const RECEIVE_MISSMATCH_ERROR_PAGE = 'RECEIVE_MISSMATCH_ERROR_PAGE';
export const REQUEST_MISSMATCH_ERROR_PAGE = 'REQUEST_MISSMATCH_ERROR_PAGE';
export const ERROR_RESOLVING              = 'ERROR_RESOLVING';
export const ERROR_RESOLVED               = 'ERROR_RESOLVED';
export const ERROR_DELETING               = 'ERROR_DELETING';
export const ERROR_DELETED                = 'ERROR_DELETED';

export const fetchPage = getRequest(
    createAction(REQUEST_MISSMATCH_ERROR_PAGE),
    createAction(RECEIVE_MISSMATCH_ERROR_PAGE),
    'api/v1/sangria/auc-metrics/user-miss-matches'
);

export const fetchResponseHandler = (response) => {
    if (!response.ok) {
        throw response;
    } else {
        return response.json();
    }
}

export const resolveMissMatch = (missmatch, member)  => dispatch => {
    let payload = {
        member_id : member.id
    }
    return putRequest(
        createAction(ERROR_RESOLVING),
        createAction(ERROR_RESOLVED),
        `api/v1/sangria/auc-metrics/user-miss-matches/${missmatch.id}`,
        payload
    )({})(dispatch);
}

export const deleteError = (missmatch)  => dispatch => {
    return deleteRequest(
        createAction(ERROR_DELETING),
        createAction(ERROR_DELETED),
        `api/v1/sangria/auc-metrics/user-miss-matches/${missmatch.id}`,
        {}
    )({})(dispatch);
}

export const queryMembers = (input) => {
    input = input.split(" ");

    let filters = '';
    for(let q of input) {
        q = encodeURIComponent(q);
        if(filters != '') filters += ',';
        filters += `first_name=@${q},last_name=@${q},email=@${q}`;
    }

    return fetch(`${ApiBaseUrl}/api/public/v1/members?filter=${filters}`)
        .then(fetchResponseHandler)
        .then((json) => {
            return json.data.map((m) =>
                ({id: m.id, name: m.first_name + ' ' + m.last_name + ' (' + m.id + ')'})
            );
        })
        .catch(fetchErrorHandler);
};

export const fetchErrorHandler = (response) => {
    let code = response.status;
    let msg  = response.statusText;

    switch (code) {
        case 403:
            break;
        case 401:
            break;
        case 412:
        case 500:
            break
    }
}