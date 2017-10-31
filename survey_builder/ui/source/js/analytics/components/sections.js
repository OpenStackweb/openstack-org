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

class SurveyAnalyticsSections extends React.Component{

    render(){
        let { sections, activeSectionIndex, onSectionSelected, onPdfButtonClicked } = this.props;

        return (
            <div className="survey_sections">
                <h2>Survey Sections</h2>
                <div className="section_container">
                    { sections.map((section, index) => (
                        <div key={index} onClick={onSectionSelected} className={"section" + ((index == activeSectionIndex) ? " active": "") } data-section-id={ section.ID } data-section-idx={index}>
                            <span data-section-id={ section.ID } data-section-idx={index}>{ section.Name }</span>
                            <span data-section-id={ section.ID } data-section-idx={index}><i className="fa fa-chevron-circle-right"></i></span>
                        </div>
                    ))}
                </div>
                <div className="section_container">
                    <br/>
                    <div className="section" data-section-id="1">
                            <a href="/analytics/faq">Analytics FAQ</a>
                    </div>
                </div>
                <div className="pdf_container">
                    <div className="pdf_button" onClick={onPdfButtonClicked}>
                        <span>DOWNLOAD AS PDF</span>
                        <span><i className="fa fa-download"></i></span>
                    </div>
                </div>
            </div>
        );
    }
}

export default SurveyAnalyticsSections;