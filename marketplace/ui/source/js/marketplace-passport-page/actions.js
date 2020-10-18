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
import { getRequest, putRequest, createAction } from "~core-utils/actions";
import URI from "urijs";
import {fetchAllStories} from "../../../../../user-stories/ui/source/js/actions";

export const REQUEST_ALL = 'REQUEST_ALL';
export const RECEIVE_ALL = 'RECEIVE_ALL';
export const REQUEST_SEARCH = 'REQUEST_SEARCH';
export const RECEIVE_SEARCH = 'RECEIVE_SEARCH';
export const CHANGE_ACTIVE_VIEW = 'CHANGE_ACTIVE_VIEW';
export const CHANGE_ACTIVE_DIST = 'CHANGE_ACTIVE_DIST';
export const UPDATE_SEARCH_TEXT = 'UPDATE_SEARCH_TEXT';
export const FILTER_ITEMS = 'FILTER_ITEMS';

export const changeStateView = createAction(CHANGE_ACTIVE_VIEW);
export const changeActiveDist = createAction(CHANGE_ACTIVE_DIST);
export const updateSearchText = createAction(UPDATE_SEARCH_TEXT);
export const filterItemsMap = createAction(FILTER_ITEMS);

let hash_tags = ['date','search'];

export const loadItems = () => (dispatch) => {

    if(!window.location.hash){
        dispatch(fetchAllItems({start:0, view:'date'}));
        return;
    }

    var url_hash = URI.parseQuery(window.location.hash.substr(1));
    var url_hash_view = '';
    var url_hash_value = '';

    if(Object.keys(url_hash).length !== 0){
        hash_tags.forEach((hash) => {
            if((hash in url_hash) && url_hash[hash]){
                url_hash_view = hash;
                url_hash_value = url_hash[hash];
            }
        });

        switch (url_hash_view) {
            case 'search':
                dispatch(fetchSearchItems(url_hash_value));
                break;
            default:
                dispatch(fetchAllItems({start:0, view:'date'}));
        }
    } else {
        dispatch(fetchAllItems({start:0, view:'date'}));
    }
}

export const fetchAllItems = getRequest(
    createAction(REQUEST_ALL),
    createAction(RECEIVE_ALL),
	'api/v1/marketplace/public-cloud-passports'
);

export const fetchSearchItems = (search) => {
	return getRequest(
        createAction(REQUEST_SEARCH),
        createAction(RECEIVE_SEARCH),
		'api/v1/marketplace/public-cloud-passports'
	)({ search_term: search });
};

export const changeActiveView = (view, section, fetch_items) => (dispatch) => {
    dispatch(changeStateView({view, section}));
    if (fetch_items) {
        dispatch(fetchAllItems({start:0, view:view}));
    }
}

export const setUrlParams = (params) => {
    hash_tags.forEach((hash) => $(window).url_fragment('setParam', hash, ''));

    Object.keys(params).forEach(param => {
        $(window).url_fragment('setParam', param, params[param]);
    });

    window.location.hash = $(window).url_fragment('serialize');
}

export const formatTextForHash = (text) => {
    return text.replace(/\s\/\s|\s/g, "-").toLowerCase();
};

