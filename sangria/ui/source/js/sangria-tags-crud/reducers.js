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

import {LOADING, RECEIVE_ITEMS, ITEM_UPDATED, ITEM_DELETED} from './actions';

export const appReducer = (
    state = {
        items : [],
        loading: false
    },
    action = {}
) => {
    switch(action.type){
        case LOADING:
        {
            return {
                ...state,
                loading: true,
            }
        }
        break;
        case RECEIVE_ITEMS:
        {
            const { response } = action.payload;
            return {
                ...state,
                items: response,
                loading: false
            }
        }
        break;
        case ITEM_UPDATED:
            return {
                ...state,
                loading: false,
                items: action.payload.response,
                msg: 'Saved!',
                msg_type: 'success'
        }
        break;
        case ITEM_DELETED:
            return {
                ...state,
                loading: false,
                items: action.payload.response,
                msg: 'Deleted!',
                msg_type: 'success'
        }
        break;
        default:
            return state;
    }
};