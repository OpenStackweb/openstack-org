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

import { getRequest, putRequest, postRequest, deleteRequest, createAction } from "~core-utils/actions";
import URI from "urijs";

// actions
export const REQUEST_ANSWERS_PAGE                    = 'REQUEST_ANSWERS_PAGE';
export const RECEIVE_ANSWERS_PAGE                    = 'RECEIVE_ANSWERS_PAGE';
export const REQUEST_FREE_TEXT_QUESTIONS_BY_TEMPLATE = 'REQUEST_FREE_TEXT_QUESTIONS_BY_TEMPLATE';
export const RECEIVE_FREE_TEXT_QUESTIONS_BY_TEMPLATE = 'RECEIVE_FREE_TEXT_QUESTIONS_BY_TEMPLATE';
export const REQUEST_ADD_TAG_FREE_TEXT_ANSWER        = 'REQUEST_ADD_TAG_FREE_TEXT_ANSWER';
export const RECEIVE_ADD_TAG_FREE_TEXT_ANSWER        = 'RECEIVE_ADD_TAG_FREE_TEXT_ANSWER';
export const REQUEST_REMOVE_TAG_FREE_TEXT_ANSWER     = 'REQUEST_REMOVE_TAG_FREE_TEXT_ANSWER';
export const RECEIVE_REMOVE_TAG_FREE_TEXT_ANSWER     = 'RECEIVE_REMOVE_TAG_FREE_TEXT_ANSWER';
export const REQUEST_AUTO_TAG_FREE_TEXT_ANSWER       = 'REQUEST_AUTO_TAG_FREE_TEXT_ANSWER';
export const RECEIVE_AUTO_TAG_FREE_TEXT_ANSWER       = 'RECEIVE_AUTO_TAG_FREE_TEXT_ANSWER';
export const REQUEST_UPDATE_FREE_TEXT_ANSWER         = 'REQUEST_UPDATE_FREE_TEXT_ANSWER';
export const RECEIVE_UPDATE_FREE_TEXT_ANSWER              = 'RECEIVE_UPDATE_FREE_TEXT_ANSWER';
export const REQUEST_TAGS_FREE_TEXT_QUESTIONS_BY_TEMPLATE = 'REQUEST_TAGS_FREE_TEXT_QUESTIONS_BY_TEMPLATE';
export const RECEIVE_TAGS_FREE_TEXT_QUESTIONS_BY_TEMPLATE = 'RECEIVE_TAGS_FREE_TEXT_QUESTIONS_BY_TEMPLATE';
export const REQUEST_MERGE_TAGS_FREE_TEXT_QUESTION    = 'REQUEST_MERGE_TAGS_FREE_TEXT_QUESTION';
export const RECEIVE_MERGE_TAGS_FREE_TEXT_QUESTION    = 'RECEIVE_MERGE_TAGS_FREE_TEXT_QUESTION';
export const REQUEST_LANGUAGES_BY_QUESTION            = 'REQUEST_LANGUAGES_BY_QUESTION';
export const RECEIVE_LANGUAGES_BY_QUESTION            = 'RECEIVE_LANGUAGES_BY_QUESTION';
export const REQUEST_EXPORT_ANSWERS                   = 'REQUEST_EXPORT_ANSWERS';
export const RECEIVE_EXPORT_ANSWERS                   = 'RECEIVE_EXPORT_ANSWERS';

export const fetchAnswersPage = (params) => (dispatch) => {
    let {template_id, question_id} = params;

    getRequest(
        createAction(REQUEST_ANSWERS_PAGE),
        createAction(RECEIVE_ANSWERS_PAGE),
        `api/v1/sangria/survey-templates/${template_id}/questions/${question_id}/free-text-answers`
    )(params)(dispatch);
};

export const exportAnswers = (params) => (dispatch) => {
    let {template_id, question_id} = params;

    dispatch(createAction(REQUEST_EXPORT_ANSWERS));

    let url = URI(`api/v1/sangria/survey-templates/${template_id}/questions/${question_id}/export-answers`).query(params).toString();
    window.open(url);

    dispatch(createAction(RECEIVE_EXPORT_ANSWERS));

};

export const fetchFreeTextQuestionByTemplate = (params) => (dispatch) => {
    let {template_id} = params;

    getRequest(
        createAction(REQUEST_FREE_TEXT_QUESTIONS_BY_TEMPLATE),
        createAction(RECEIVE_FREE_TEXT_QUESTIONS_BY_TEMPLATE),
        `api/v1/sangria/survey-templates/${template_id}/questions`
    )(params)(dispatch);
};

export const addTagToFreeTextAnswer = (params, payload) => (dispatch) => {
    let {template_id, question_id, answer_id} = params;

    postRequest(
        createAction(REQUEST_ADD_TAG_FREE_TEXT_ANSWER),
        createAction(RECEIVE_ADD_TAG_FREE_TEXT_ANSWER),
        `api/v1/sangria/survey-templates/${template_id}/questions/${question_id}/free-text-answers/${answer_id}/tags`,
        payload
    )(params)(dispatch);
};

export const removeTagFromFreeTextAnswer = (params, payload) => (dispatch) => {
    let {template_id, question_id, answer_id} = params;

    deleteRequest(
        createAction(REQUEST_REMOVE_TAG_FREE_TEXT_ANSWER),
        createAction(RECEIVE_REMOVE_TAG_FREE_TEXT_ANSWER),
        `api/v1/sangria/survey-templates/${template_id}/questions/${question_id}/free-text-answers/${answer_id}/tags`,
    payload
    )(params)(dispatch);
};

export const extractTagsByKMeans = (params, payload = {}) => (dispatch) => {
    let {template_id, question_id} = params;

    putRequest(
        createAction(REQUEST_AUTO_TAG_FREE_TEXT_ANSWER),
        createAction(RECEIVE_AUTO_TAG_FREE_TEXT_ANSWER),
        `api/v1/sangria/survey-templates/${template_id}/questions/${question_id}/free-text-answers/tagging/kmeans`,
    payload
    )(params)(dispatch);
};

export const extractTagsByRegex = (params, payload = {}) => (dispatch) => {
    let {template_id, question_id} = params;

    putRequest(
        createAction(REQUEST_AUTO_TAG_FREE_TEXT_ANSWER),
        createAction(RECEIVE_AUTO_TAG_FREE_TEXT_ANSWER),
        `api/v1/sangria/survey-templates/${template_id}/questions/${question_id}/free-text-answers/tagging/regex`,
        payload
    )(params)(dispatch);
};

export const extractTagsByRAKE = (params, payload = {}) => (dispatch) => {
    let {template_id, question_id} = params;

    putRequest(
        createAction(REQUEST_AUTO_TAG_FREE_TEXT_ANSWER),
        createAction(RECEIVE_AUTO_TAG_FREE_TEXT_ANSWER),
        `api/v1/sangria/survey-templates/${template_id}/questions/${question_id}/free-text-answers/tagging/rake`,
        payload
    )(params)(dispatch);
};

export const updateFreeTextAnswer = (params, payload = {}) => (dispatch) => {
    let {template_id, question_id, answer_id} = params;
    putRequest(
        createAction(REQUEST_UPDATE_FREE_TEXT_ANSWER),
        createAction(RECEIVE_UPDATE_FREE_TEXT_ANSWER),
        `api/v1/sangria/survey-templates/${template_id}/questions/${question_id}/free-text-answers/${answer_id}`,
        payload
    )(params)(dispatch);
};

export const fetchFreeTextAnswersTagsByQuestion =  (params) => (dispatch) => {
    let {template_id, question_id } = params;

    getRequest(
        createAction(REQUEST_TAGS_FREE_TEXT_QUESTIONS_BY_TEMPLATE),
        createAction(RECEIVE_TAGS_FREE_TEXT_QUESTIONS_BY_TEMPLATE),
        `api/v1/sangria/survey-templates/${template_id}/questions/${question_id}/free-text-answers/all/tags`
    )(params)(dispatch);
};

export const showFreeTextAnswersStatsView = (params) => (dispatch) => {

    let url = URI('sangria/ViewSurveyFreeAnswersStats').query(params).toString();
    window.open(url);
};

export const mergeTags = (params, payload) => (dispatch) => {
    let {template_id, question_id} = params;

    postRequest(
        createAction(REQUEST_MERGE_TAGS_FREE_TEXT_QUESTION),
        createAction(RECEIVE_MERGE_TAGS_FREE_TEXT_QUESTION),
        `api/v1/sangria/survey-templates/${template_id}/questions/${question_id}/free-text-answers/merge_tags`,
        payload
    )(params)(dispatch);
};

export const fetchLanguagesByQuestion = (params) => (dispatch) => {
    let {template_id, question_id} = params;

    getRequest(
        createAction(REQUEST_LANGUAGES_BY_QUESTION),
        createAction(RECEIVE_LANGUAGES_BY_QUESTION),
        `api/v1/sangria/survey-templates/${template_id}/questions/${question_id}/languages`
)(params)(dispatch);
};