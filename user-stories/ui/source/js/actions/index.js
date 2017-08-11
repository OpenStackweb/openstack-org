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


import { getRequest, putRequest, createAction } from "~core-utils/actions";

export const REQUEST_ALL_STORIES = 'REQUEST_ALL_STORIES';
export const RECEIVE_ALL_STORIES = 'RECEIVE_ALL_STORIES';
export const REQUEST_SEARCH_STORIES = 'REQUEST_SEARCH_STORIES';
export const RECEIVE_SEARCH_STORIES = 'RECEIVE_SEARCH_STORIES';
export const CHANGE_ACTIVE_VIEW = 'CHANGE_ACTIVE_VIEW';
export const CHANGE_ACTIVE_DIST = 'CHANGE_ACTIVE_DIST';
export const UPDATE_SEARCH_TEXT = 'UPDATE_SEARCH_TEXT';
export const REQUEST_TAG_SEARCH_STORIES = 'REQUEST_TAG_SEARCH_STORIES';

export const changeStateView = createAction(CHANGE_ACTIVE_VIEW);
export const changeActiveDist = createAction(CHANGE_ACTIVE_DIST);
export const updateSearchText = createAction(UPDATE_SEARCH_TEXT);

export const fetchAllStories = getRequest(
    createAction(REQUEST_ALL_STORIES),
    createAction(RECEIVE_ALL_STORIES),
	'api/v1/user-stories'
);

export const fetchSearchStories = (search) => {
	return getRequest(
        createAction(REQUEST_SEARCH_STORIES),
        createAction(RECEIVE_SEARCH_STORIES),
		'api/v1/user-stories'
	)({ search_term: search });
};

export const fetchSearchTagStories = (tag) => {
    return getRequest(
        createAction(REQUEST_TAG_SEARCH_STORIES),
        createAction(RECEIVE_SEARCH_STORIES),
        'api/v1/user-stories'
    )({ tag: tag });
};

export const changeActiveView = (view, fetch_stories) => (dispatch) => {
    dispatch(changeStateView(view));
    if (fetch_stories) {
        dispatch(fetchAllStories({start:0, view:view}));
    }
}

