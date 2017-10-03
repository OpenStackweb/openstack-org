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
    UPDATE_EVENT,
    SUBMITING_NEW_COMMENT,
    SUBMITTED_NEW_COMMENT,
} from '../actions'

const DEFAULT_STATE = {
    event: null,
    currentUser: null,
};

/**
 * @param state
 * @param action
 * @returns {{events: null, currentUser: null}}
 * @constructor
 */
export const scheduleEventDetailsReducer = (state = DEFAULT_STATE, action) => {
    const {type, payload} = action;
    switch (type) {

        case UPDATE_EVENT:
            var { mutator } = payload;
            return { ...state, event: mutator(state.event)};
        case SUBMITING_NEW_COMMENT:
            return { ...state, currentUser: { ... state.currentUser, has_feedback : true }};
        case SUBMITTED_NEW_COMMENT:
            let newComment  = payload;
            let postedComment = {
                id : newComment.id,
                rate: newComment.rating,
                date: 'just now',
                note: newComment.comment,
            };
            let event = { ...state.event, comments: [postedComment, ...state.event.comments] };
            return { ...state, event: event};
        default:
            return state;
    }
};