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
import {
    CLEAR_MESSAGE,
    SHOW_MESSAGE,
    STOP_LOADING,
} from './actions';

export const genericReducers  = function (
state = {
    msg: null,
    msg_type: null,
    params: {},
    loading: false,
},
action = {}) {
    switch(action.type) {
        case SHOW_MESSAGE:
            return {
                ...state,
                msg: action.payload.msg,
                msg_type: action.payload.msg_type,
            };
        case CLEAR_MESSAGE:
            return {
                ...state,
                msg: null
            };
        case STOP_LOADING:  return {
            ...state,
            loading: false
        };
        default:
            return state;
    }
};
