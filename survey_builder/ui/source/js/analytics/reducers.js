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
    REQUEST_REPORT_TEMPLATE,
    RECEIVE_REPORT_TEMPLATE,
    REQUEST_REPORT,
    RECEIVE_REPORT,
    FILTER_SELECTED,
    CLEAR_FILTERS,
    SELECTED_SECTION
} from './actions';

const AnalyticReducer  = function (
    state = {
        filters: [],
        sections: [],
        loading: false,
        selectedFilters: {},
        report: null,
        activeSectionId: 0,
        activeSectionIndex: 0
    },
    action = {}) {
    switch(action.type) {
        case SELECTED_SECTION:
        {
            const { sectionId , sectionIndex} = action.payload;
            return {
                ...state,
                activeSectionId: sectionId,
                activeSectionIndex: sectionIndex
            }
        }
        case FILTER_SELECTED:{
            const { questionId, value } = action.payload;
            return {
                ...state,
                selectedFilters: {...state.selectedFilters,  [questionId]: value}
            }
        }
        case CLEAR_FILTERS:{
            return {
                ...state,
                selectedFilters: {}
            }
        }
        case REQUEST_REPORT_TEMPLATE:{
            return {
                ...state,
                loading: true,
            }
        }
        case REQUEST_REPORT:
        {
            return {
                ...state,
                loading: true,
            }
        }
        case RECEIVE_REPORT:{
            let report = action.payload.response;
            return {
                ...state,
                loading: false,
                report
            };
        }
        case RECEIVE_REPORT_TEMPLATE:{
            const { Filters, Sections } = action.payload.response;
            return {
                ...state,
                loading: false,
                filters: [...Filters],
                sections: [...Sections],
                activeSectionIndex: 0,
                activeSectionId: Sections[0].ID,
            };
        }
        default:
            return state;
    }
};

export default AnalyticReducer;