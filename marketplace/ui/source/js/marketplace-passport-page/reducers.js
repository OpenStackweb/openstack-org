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
    setUrlParams,
    REQUEST_ALL,
    RECEIVE_ALL,
    CHANGE_ACTIVE_VIEW,
    CHANGE_ACTIVE_DIST,
    REQUEST_SEARCH,
    RECEIVE_SEARCH,
    UPDATE_SEARCH_TEXT,
    FILTER_ITEMS
} from './actions';

export const appReducer = function (
	state = {
        items: [],
        loading: false,
        active_view: 'date',
        section: '',
        distribution: 'tiles'
	},
	action = {}) {
        let response = null;

		switch(action.type) {
			case REQUEST_ALL:
				return {
					...state,
					loading: true
				};
            case RECEIVE_ALL:
                response = action.payload.response;

				return {
					...state,
					loading: false,
                    items: response.items,
                    has_more: response.has_more,
                    total: response.total,
                    search: ''
				};
            case REQUEST_SEARCH:
                setUrlParams({search: action.payload.search_term});
                return {
                    ...state,
                    search: action.payload.search_term,
                    loading: true,
                };
            case RECEIVE_SEARCH:
                response = action.payload.response;

                return {
                    ...state,
                    loading: false,
                    items: response.items,
                    has_more: response.has_more,
                    total: response.total,
                    active_view: 'search',
                };
            case CHANGE_ACTIVE_VIEW:
                setUrlParams({[action.payload.view]: action.payload.section});

                return {
                    ...state,
                    active_view: action.payload.view,
                    section: action.payload.section
                };
            case CHANGE_ACTIVE_DIST:
                return {
                    ...state,
                    distribution: action.payload
                };
            case UPDATE_SEARCH_TEXT:
                return {
                    ...state,
                    search: action.payload
                };
            case FILTER_ITEMS:
                return {
                    ...state,
                    items: action.payload
                };

			default:
				return state;

		}
};


/*eslint-enable */
