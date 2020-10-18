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

import {RECEIVE_PAGE, REQUEST_PAGE, UPDATING, UPDATED} from './actions';

export const appReducer = (
    state = {
        items : [],
        page_count: 0,
        page_size: 0,
        loading: false,
    },
    action = {}
) => {
    switch(action.type){
        case REQUEST_PAGE:
        {
            const { page_size } = action.payload;
            return {
                ...state,
                page_size: page_size,
                loading: true,
            }
        }
        break;
        case RECEIVE_PAGE:
        {
            const { response } = action.payload;
            let page_count     = state.page_size > 0 ? parseInt(Math.ceil(response.count/state.page_size)) : 0;
            console.log(`page_count ${page_count}`);
            return {
                ...state,
                items: response.items,
                page_count ,
                loading: false
            }
        }
        break;
        case UPDATING:
            return {
                ...state,
                loading: true
            }
            break;
        case UPDATED:
            return {
                ...state,
                loading: false,
                msg: 'Saved!',
                msg_type: 'success'
        }
        break;
        default:
            return state;
    }
};