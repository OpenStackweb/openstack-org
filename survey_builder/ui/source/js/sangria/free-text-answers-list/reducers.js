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
    REQUEST_ANSWERS_PAGE,
    RECEIVE_ANSWERS_PAGE,
    REQUEST_FREE_TEXT_QUESTIONS_BY_TEMPLATE,
    RECEIVE_FREE_TEXT_QUESTIONS_BY_TEMPLATE,
    REQUEST_AUTO_TAG_FREE_TEXT_ANSWER,
    RECEIVE_AUTO_TAG_FREE_TEXT_ANSWER,
    REQUEST_UPDATE_FREE_TEXT_ANSWER,
    RECEIVE_UPDATE_FREE_TEXT_ANSWER,
    REQUEST_TAGS_FREE_TEXT_QUESTIONS_BY_TEMPLATE,
    RECEIVE_TAGS_FREE_TEXT_QUESTIONS_BY_TEMPLATE,
    REQUEST_MERGE_TAGS_FREE_TEXT_QUESTION,
    RECEIVE_MERGE_TAGS_FREE_TEXT_QUESTION,
    REQUEST_LANGUAGES_BY_QUESTION,
    RECEIVE_LANGUAGES_BY_QUESTION,
    REQUEST_EXPORT_ANSWERS,
    RECEIVE_EXPORT_ANSWERS
} from './actions';

import {
    SHOW_MESSAGE
} from "~core-utils/actions";

export const surveyFreeTextAnswersReducer = (
    state = {
        template_id: 0,
        question_id:0,
        questions: [],
        answers : [],
        languages : ['All'],
        selected_languages: [],
        tags: [],
        page_count: 0,
        page_size: 25,
        current_page: 1,
        loading: false,
        reload: false,
        open_modal_show_stats: false
    },
    action = {}
) => {
    let response = '';

    switch(action.type){
        case REQUEST_ANSWERS_PAGE:
        {
            const { page_size, page, question_id, search_term, languages } = action.payload;
            let loading = search_term == '' ? true : false;
            return {
                ...state,
                page_size: page_size,
                loading: loading,
                current_page: page,
                question_id: question_id,
                reload: false,
                open_modal_show_stats: false,
                search_term: search_term,
                selected_languages: languages
            }
        }
        break;
        case RECEIVE_ANSWERS_PAGE:
        {
            response = action.payload.response;
            let page_count     = state.page_size > 0 ? parseInt(Math.ceil(response.count/state.page_size)) : 0;
            return {
                ...state,
                answers: response.items,
                page_count ,
                loading: false
            }
        }
        break;
        case REQUEST_FREE_TEXT_QUESTIONS_BY_TEMPLATE:
        {
            const { template_id } = action.payload;
            return {
                ...state,
                template_id: template_id,
                loading: true,
                answers:[],
                page_count: 0,
                current_page: 1,
                page_size: 25,
                question_id: 0
            }
        }
        break;
        case RECEIVE_FREE_TEXT_QUESTIONS_BY_TEMPLATE:
        {
            response = action.payload.response;
            return {
                ...state,
                questions: response.items,
                loading: false
            }
        }
        break;
        case REQUEST_AUTO_TAG_FREE_TEXT_ANSWER:
            return {
                ...state,
                loading: true,
            }
            break;
        case RECEIVE_AUTO_TAG_FREE_TEXT_ANSWER:
            return {
                ...state,
                loading: false,
                reload: true,
            }
            break;
        case REQUEST_UPDATE_FREE_TEXT_ANSWER:
            return {
                ...state,
                loading: true,
            }
            break;
        case RECEIVE_UPDATE_FREE_TEXT_ANSWER:
            return {
                ...state,
                loading: false,
            }
            break;
        case SHOW_MESSAGE:
            return {
                ...state,
                loading: false
            };
            break;
        case REQUEST_TAGS_FREE_TEXT_QUESTIONS_BY_TEMPLATE:
            return {
                ...state,
                loading: true,
                tags: [],
            };
            break;
        case RECEIVE_TAGS_FREE_TEXT_QUESTIONS_BY_TEMPLATE:
            response = action.payload.response;
            let tags = [];
            for(let tag of response.items){
                tags.push(tag.value);
            }
            console.log(tags);
            return {
                ...state,
                tags: tags,
                loading: false,
            };
            break;
        case REQUEST_MERGE_TAGS_FREE_TEXT_QUESTION:
            return {
                ...state,
                loading: true,
            };
            break;
        case RECEIVE_MERGE_TAGS_FREE_TEXT_QUESTION:
            window.location.reload();
            break;
        case REQUEST_LANGUAGES_BY_QUESTION:
            return {
                ...state,
                loading: true,
            };
            break;
        case RECEIVE_LANGUAGES_BY_QUESTION:
            response = action.payload.response;
            return {
                ...state,
                languages: response.items,
                loading: false
            }
            break;
        case REQUEST_EXPORT_ANSWERS:
            return {
                ...state,
                loading: true,
            };
            break;
        case RECEIVE_EXPORT_ANSWERS:
            return {
                ...state,
                loading: false
            }
            break;
        default:
            return state;
        break;
    }
};