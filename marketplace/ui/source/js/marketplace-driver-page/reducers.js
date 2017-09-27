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

import {RECEIVE_DRIVERS, SHOW_LOADING} from './actions';

export const appReducer = (
    state = {
        items : [],
        loading: false,
    },
    action = {}
) => {
    switch(action.type){
        case SHOW_LOADING:
        {
            return {
                ...state,
                loading: true,
            }
        }
        break;
        case RECEIVE_DRIVERS:
        {
            const { response } = action.payload;
            return {
                ...state,
                items: response,
                loading: false
            }
        }
        break;
        case 'TOGGLE_FOR_GROUP':
            return {
                ...state,
                items: action.payload
            };
        break;
        default:
            return state;
    }
};