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

import {RECEIVE_ITEMS, REQUEST_ITEMS, RECEIVE_META} from './actions';
import { START_LOADING, STOP_LOADING } from "openstack-uicore-foundation/lib/actions";

export const appReducer = (
    state = {
        loading: false,
        items : [],
        selectedReleases: [],
        page: 1,
        lastPage: 10,
        order: 'last_name',
        orderDir: 1,
        allReleases: []
    },
    action = {}
) => {
    switch(action.type){
        case START_LOADING:
        {
            return {
                ...state,
                loading: true,
            }
        }
        break;
        case STOP_LOADING:
        {
            return {
                ...state,
                loading: false,
            }
        }
            break;
        case RECEIVE_ITEMS:
        {
            const { response } = action.payload;
            return {
                ...state,
                items: response.data,
                lastPage: response.totalPages
            }
        }
        break;
        case REQUEST_ITEMS:
        {
            let {order, orderDir, releases, page} = action.payload;

            return {...state, order, orderDir, selectedReleases: releases, page }
        }
            break;
        case RECEIVE_META:
        {
            const { response } = action.payload;

            return {...state, allReleases: response }
        }
            break;
        default:
            return state;
    }
};