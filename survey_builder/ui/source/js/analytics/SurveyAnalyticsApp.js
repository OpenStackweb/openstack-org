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
import {getSurveyReportTemplate, selectedFilter, clearFilters, onSelectedSection, getReport } from './actions';
import { connect } from 'react-redux';
import { AjaxLoader } from '~core-components/ajaxloader';
import SurveyAnalyticsSections from './components/sections';
import SurveyAnalyticsFilters from './components/filters';
import SurveyAnalyticsDashboard from './components/dashboard';

class SurveyAnalyticsApp extends React.Component {

    constructor(props){
        super(props);
        this.onChangeTemplate   = this.onChangeTemplate.bind(this);
        this.onFilterSelected   = this.onFilterSelected.bind(this);
        this.clearFilters       = this.clearFilters.bind(this);
        this.onSectionSelected  = this.onSectionSelected.bind(this);
        this.onPdfButtonClicked = this.onPdfButtonClicked.bind(this)
    }


    getReportData(){
        let templateId = this.selectReportTemplates.value;
        this.props.getReport(templateId);
    }

    getReportTemplateData(){
        let templateId = this.selectReportTemplates.value;
        this.props.getSurveyReportTemplate(templateId);
    }

    componentDidMount() {
        this.getReportTemplateData();
    }

    componentDidUpdate(prevProps, prevState){
        if(prevProps.activeSectionId != this.props.activeSectionId
          || prevProps.selectedFilters != this.props.selectedFilters){
            this.getReportData();
        }
    }

    onFilterSelected(event){
        let ddl         = event.currentTarget;
        let filterValue = ddl.value;
        let questionId  = ddl.getAttribute('data-question-id');
        console.log(`filterValue ${filterValue} - questionId ${questionId} selected`);
        this.props.selectedFilter({questionId: questionId, value:filterValue});
    }

    onSectionSelected(event){
        let section      = event.target;
        let sectionId    = section.getAttribute('data-section-id');
        let sectionIndex = section.getAttribute('data-section-idx');
        console.log(`selected section id ${sectionId}`);
        this.props.onSelectedSection({sectionId, sectionIndex});
    }

    onPdfButtonClicked(event){
        let { report } = this.props;
        if(!report) return false;
        let doc = new jsPDF("p","mm","a4");

        doc.setFont("times","normal");
        doc.setTextColor(42, 78, 104);
        doc.setDrawColor(42, 78, 104);

        doc.setFontSize(22);
        doc.text(25, 30, report.Name);
        doc.line(25, 35, 185, 35);

        doc.setFontSize(14);
        let desc = $('.section_desc').text();
        let split_desc = (desc.length > 60) ? doc.splitTextToSize(desc, 160) : desc;
        doc.text(25, 45, split_desc );

        var graph_count = $('.graph_box').length;
        var pos_x,pos_y,height,width,ratio,page_height,page_width;

        $('.graph_box').each((idx,element) => {
            html2canvas($(element), {
                onrendered: (canvas) => {
                    page_height = doc.internal.pageSize.height;
                    page_width = doc.internal.pageSize.width;
                    pos_x = 25;
                    pos_y = (graph_count == $('.graph_box').length) ? 50 : pos_y + height + 10;
                    let page_width_wom = page_width - (pos_x * 2); // page width without margins
                    height = Math.round($(element).outerHeight() * 0.26);
                    width = Math.round($(element).outerWidth() * 0.26);

                    if (width > page_width_wom) {
                        ratio = page_width_wom / width;
                        width = page_width_wom;
                        height = Math.round(height * ratio);
                    }

                    if ((pos_y + height) > page_height) {
                        doc.addPage();
                        pos_y = 30; // Restart height position
                    }
                    let imgData = canvas.toDataURL("image/jpeg",1.0);
                    doc.addImage(imgData, 'JPEG', pos_x, pos_y, width, height);
                    if (!--graph_count) doc.save(`${report.Name}.pdf`);
                },
            });
        });
    }

    clearFilters(event){
        console.log('clear filters');
        this.props.clearFilters();
    }

    onChangeTemplate(event){
        let templateId = event.target.value;
        console.log(`templateId ${templateId} selected`);
        this.props.getSurveyReportTemplate(templateId);
    }

    render(){
        let {
            reportTemplates,
            filters,
            selectedFilters,
            sections,
            activeSectionId,
            activeSectionIndex,
            report
        } = this.props;

        return(
            <div>
                <AjaxLoader show={ this.props.loading } size={ 120 }/>
                <h1>OpenStack Survey Report</h1>
                <div className="container">
                    <div className="report_templates_container">
                        <select ref={(input) => this.selectReportTemplates = input}  id="report-templates" className="form-control" onChange={this.onChangeTemplate}>
                            { reportTemplates.map((template, index) => (
                                <option key={index} value={template.id}>{template.title}</option>
                            ))}
                        </select>
                    </div>
                </div>
                <SurveyAnalyticsFilters
                    selectedFilters={selectedFilters}
                    filters={filters}
                    onFilterSelected={this.onFilterSelected}
                    clearFilters={this.clearFilters}/>
                <div className="container">
                    <div className="row">
                        <div className="col-md-3">
                            <SurveyAnalyticsSections
                                sections={sections}
                                activeSectionId={activeSectionId}
                                activeSectionIndex={activeSectionIndex}
                                onSectionSelected={this.onSectionSelected}
                                onPdfButtonClicked={this.onPdfButtonClicked} />
                        </div>
                        <div className="col-md-9" id="dashboard-container">
                            {report &&
                                <SurveyAnalyticsDashboard report={report}/>
                            }
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

function mapStateToProps(state) {
    return {
        filters: state.filters,
        sections: state.sections,
        loading: state.loading,
        selectedFilters: state.selectedFilters,
        activeSectionId: state.activeSectionId,
        activeSectionIndex: state.activeSectionIndex,
        report: state.report,
    }
}

export default connect(mapStateToProps, {
    getSurveyReportTemplate,
    selectedFilter,
    clearFilters,
    onSelectedSection,
    getReport,
})(SurveyAnalyticsApp)