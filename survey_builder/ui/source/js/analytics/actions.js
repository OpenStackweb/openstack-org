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
import 'sweetalert2/dist/sweetalert2.css';
import swal from 'sweetalert2';

export const REQUEST_REPORT_TEMPLATE = 'REQUEST_REPORT_TEMPLATE';
export const RECEIVE_REPORT_TEMPLATE = 'RECEIVE_REPORT_TEMPLATE';
export const REQUEST_REPORT          = 'REQUEST_REPORT';
export const RECEIVE_REPORT          = 'RECEIVE_REPORT';
export const FILTER_SELECTED         = 'FILTER_SELECTED';
export const CLEAR_FILTERS           = 'CLEAR_FILTERS';
export const SELECTED_SECTION        = 'SELECTED_SECTION';

const errorHandler = (err, res) => {
    let text = res.body;
    if(res.body != null && res.body.messages instanceof Array) {
        let messages = res.body.messages.map( m => {
            if (m instanceof Object) return m.message
            else return m;
        })
        text = messages.join('\n');
    }
    swal(res.statusText, text, "error");
}

export const getSurveyReportTemplate = (templateId) => (dispatch, getState) => {
    getRequest(
        createAction(REQUEST_REPORT_TEMPLATE),
        createAction(RECEIVE_REPORT_TEMPLATE),
        `api/v1/survey_report/report_template/${templateId}`
    )({})(dispatch, getState).then(() => {
        getReport(templateId)(dispatch, getState);
    });
};

export const selectedFilter = (params) => (dispatch) => {
    dispatch(createAction(FILTER_SELECTED)(params));
}

export const clearFilters = (params) => (dispatch) => {
    dispatch(createAction(CLEAR_FILTERS)());
}

export const onSelectedSection = (params) => (dispatch) => {
    dispatch(createAction(SELECTED_SECTION)(params));
}

export const getReport = (templateId) => (dispatch, getState) => {
    console.log(`calling getReport with templateId ${templateId}`);
    let { selectedFilters, activeSectionId } = getState();
    let filters = [];
    Object.keys( selectedFilters ).forEach( questionId => {
        filters.push({id: questionId, value: selectedFilters[questionId]});
    });
    getRequest(
        createAction(REQUEST_REPORT),
        createAction(RECEIVE_REPORT),
        `api/v1/survey_report/report/${templateId}`
    )({filters: JSON.stringify(filters), section_id: activeSectionId})(dispatch);
};