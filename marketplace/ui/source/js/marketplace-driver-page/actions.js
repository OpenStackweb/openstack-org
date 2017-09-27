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

import { getRequest, createAction } from "~core-utils/actions";

import URI from "urijs";

export const SHOW_LOADING = 'SHOW_LOADING';
export const RECEIVE_DRIVERS = 'RECEIVE_DRIVERS';
export const REORDER_ITEMS = 'REORDER_ITEMS';

const reorderItems = createAction(REORDER_ITEMS);

export const fetchAll = getRequest(
    createAction(SHOW_LOADING),
    createAction(RECEIVE_DRIVERS),
    'api/v1/marketplace/drivers'
);

export const fetchOrderedItems = (params) => dispatch => {
    let items = params.items;
    let sort_field = params.sort_field.toLowerCase();
    let sort_dir = params.sort_dir == 'ASC' ? 1 : -1;

    items.sort((a,b) => {
        const aName = a[sort_field].toUpperCase();
        const bName = b[sort_field].toUpperCase();

        return (aName > bName ? 1 : (aName < bName ? -1 : 0)) * sort_dir;
    });

    dispatch(reorderItems(items));
}


