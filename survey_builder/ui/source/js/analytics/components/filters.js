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
import React from 'react';

const SurveyAnalyticsFilters = ({filters, onFilterSelected, clearFilters, selectedFilters}) =>
{
    return (
        <div className="report_global_filters">
            <div className="container">
                <div className="row">
                    <div className="report_global_filters_title">GLOBAL FILTERS</div>
                    <div className="report_clear_filters" onClick={clearFilters}>
                                <span className="fa-stack fa-lg">
                                    <i className="fa fa-circle fa-stack-2x"></i>
                                    <i className="fa fa-times fa-stack-1x fa-inverse"></i>
                                </span>
                        clear all filters
                    </div>
                </div>
                <div className="row">
                    { filters.map((filter, index) => (
                        <div key={index} className={"report_filter_box" + ((filters.length-1) == index ? " last":"") }>
                            <select onChange={onFilterSelected} data-question-id={ filter.Question } value={(selectedFilters.hasOwnProperty(`${filter.Question}`)) ? selectedFilters[filter.Question]:''} className="report_filter form-control">
                                <option value="" disabled={true}>{ filter.Label }</option>
                                { filter.Options.map((option, index2) => (
                                    <option key={index2} value={ option.id }>{ option.value }</option>
                                ))}
                            </select>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
};

SurveyAnalyticsFilters.propTypes = {
    filters: React.PropTypes.array.isRequired,
    onFilterSelected: React.PropTypes.func.isRequired,
    clearFilters: React.PropTypes.func.isRequired,
    selectedFilters: React.PropTypes.object.isRequired
};

export default SurveyAnalyticsFilters;