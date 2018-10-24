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
import { connect } from 'react-redux';
import { AjaxLoader } from '~core-components/ajaxloader';
import Message from "~core-components/message";

import {
    fetchAnswersPage,
    fetchFreeTextQuestionByTemplate,
    addTagToFreeTextAnswer,
    removeTagFromFreeTextAnswer,
    extractTagsByKMeans,
    extractTagsByBayes,
    extractTagsByRAKE,
    updateFreeTextAnswer,
    fetchFreeTextAnswersTagsByQuestion,
    showFreeTextAnswersStatsView,
    fetchLanguagesByQuestion,
    exportAnswers,
    rebuildBayesianNaiveModel,
} from './actions';

import {
    Modal,
    ModalHeader,
    ModalTitle,
    ModalClose,
    ModalBody,
    ModalFooter
} from 'react-modal-bootstrap';

import {SurveyTemplateSelector} from './components/SurveyTemplateSelector';
import {SurveyTemplateQuestionSelector} from './components/SurveyTemplateQuestionSelector';
import {SurveyLanguageSelector} from './components/SurveyLanguageSelector';
import {TagInput} from './components/TagInput';

const MAX_CLUSTER_QTY = 25;

class SangriaSurveyFreeTextAnswersListApp extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            isOpenModalExtractTags  : false,
        }
    }

    onChangeSurveyTemplate(e){
        let target = e.currentTarget;
        let val    = target.value;
        if(val > 0)
            this.props.fetchQuestions(val);

        $('.filters-container').addClass('hidden');
    }

    onChangeSurveyTemplateQuestion(e){
        let target = e.currentTarget;
        let val    = target.value;

        this.props.fetchLanguages(this.props.template_id, val);

        this.props.fetchFreeTextAnswers
        (
            this.props.template_id,
            1,
            this.props.page_size,
            '',
            val,
            this.props.search_term,
            this.props.selected_languages
        );
        $('.filters-container').removeClass('hidden');
    }

    onChangeLanguage(values){
        let selected_languages = values;

        this.props.fetchFreeTextAnswers
            (
                this.props.template_id,
                1,
                this.props.page_size,
                '',
                this.props.question_id,
                this.props.search_term,
                selected_languages
            );
    }

    componentDidUpdate(prevProps, prevState){
        if(this.props.reload == true) {
            this.props.fetchFreeTextAnswers
            (
                this.props.template_id,
                this.props.current_page,
                this.props.page_size,
                '',
                this.props.question_id,
                this.props.search_term,
                this.props.selected_languages
            );
        }
    }

    onTagRemoved(e, answer_id){
        this.props.removeTag(e.item, this.props.template_id, this.props.question_id, answer_id);
    }

    onTagAdded(e, answer_id){
        this.props.addTag(e.item, this.props.template_id, this.props.question_id, answer_id);
    }

    onChangePageSize(e) {
        let target    = e.currentTarget;
        let page_size = target.value;

        this.props.fetchFreeTextAnswers
        (
            this.props.template_id,
            1,
            page_size,
            '',
            this.props.question_id,
            this.props.search_term,
            this.props.selected_languages
        );
    }

    onChangePage(e){
        e.preventDefault();
        let target         = e.currentTarget;
        let current_page   = target.attributes.getNamedItem('data-page').value;

        this.props.fetchFreeTextAnswers
        (
            this.props.template_id,
            current_page,
            this.props.page_size,
            '',
            this.props.question_id,
            this.props.search_term,
            this.props.selected_languages
        );
    }

    onExtractTags(e){
        let method = this.selectTagsExtractionMethod.value;
        console.log(`selected method ${method}`);
        if(this.props.question_id > 0 && this.props.template_id > 0 && method != '') {

            let deleteFormer = this.deleteFormerExtractedTags.checked ? 1 : 0;
            let clusters     = this.selectTagsExtractionMethodClusters.value;

            switch(method) {
                case 'KMEANS':
                    this.props.calculateTagsByKMEANS(this.props.template_id, this.props.question_id, deleteFormer , clusters);
                    this.hideModalExtractTags();
                break;
                case 'RAKE':
                    this.props.calculateTagsByRAKE(this.props.template_id, this.props.question_id, deleteFormer);
                    this.hideModalExtractTags();
                break;
                case 'BAYES':
                    this.props.calculateTagsByBayes(this.props.template_id, this.props.question_id, deleteFormer);
                    this.hideModalExtractTags();
                break;
            }
        }
    }

    onRebuildBayesianNaiveModel(e){
        e.preventDefault();
        this.props.rebuildBayesianNaiveModel();
    }

    onExtractTagsClicked(e){
        this.openModalExtractTags();
    }

    onExportResultsClicked(e){
        this.props.exportFreeTextAnswers
            (
                this.props.template_id,
                this.props.question_id,
                this.props.search_term,
                this.props.selected_languages
            );
    }

    onShowStatsClicked(e){
        this.props.showStatsView(this.props.template_id, this.props.question_id);
    }

    onMergeTagsClicked(e){
        this.openModalMergeTags();
        this.props.fetchTagsByQuestion(this.props.template_id, this.props.question_id);
    }

    openModalExtractTags() {
        this.setState({
            isOpenModalExtractTags: true
        });
    };

    hideModalExtractTags(){
        this.setState({
            isOpenModalExtractTags: false
        });
    };

    onChangeTagExtractionMethod(e){
        let target = e.currentTarget;
        let val    = target.value;
        if(!$('.bayes-control-group').hasClass('hidden'))
            $('.bayes-control-group').addClass('hidden');
        if(!$('.kmean-control-group').hasClass('hidden'))
            $('.kmean-control-group').addClass('hidden');
        if(val == 'KMEANS'){
            $('.kmean-control-group').removeClass('hidden');
            return;
        }
        if(val == 'BAYES'){
            $('.bayes-control-group').removeClass('hidden');
            return;
        }
    }

    onUpdateAnswerValueClicked(e, answerId){
        let val = $(`#answer_value_textarea_${answerId}`).val();
        this.props.updateAnswerValue(this.props.template_id, this.props.question_id, answerId, val);
    }

    onViewSurveyClicked(e, surveyId){
        window.open(`/sangria/SurveyDetails/${surveyId}`, '_blank');
    }

    onFilterByTag(e){
        let target = e.currentTarget;
        let val    = target.value;
        console.log('onFilterByTag value '+ val);
        this.props.fetchFreeTextAnswers
        (
            this.props.template_id,
            this.props.current_page,
            this.props.page_size,
            '',
            this.props.question_id,
            val,
            this.props.selected_languages
        );
    }

    render() {

        // build pagination ...
        let pages = [];
        for(let i = 0; i < this.props.page_count; i++)
            pages.push
            (
                <li key={i} className={ (i+1) == this.props.current_page ? "active" : "" }>
                    <a href="#" data-page={i+1} onClick={(e) => this.onChangePage(e)}>{i+1}</a>
                </li>
            );
        let cluster_options = [];

        for(let i = 2;  i <= MAX_CLUSTER_QTY ; i++ ){
            cluster_options.push(
                <option key={i} value={i}>{i}</option>
            );
        }

        return (
            <div>
                <Message />
                <AjaxLoader show={ this.props.loading } size={ 75 }/>
                <Modal isOpen={this.state.isOpenModalExtractTags} onRequestHide={(e) => this.hideModalExtractTags()}>
                    <ModalHeader>
                        <ModalClose onClick={ (e) => this.hideModalExtractTags()}/>
                        <ModalTitle>Extract Tags from Answers</ModalTitle>
                    </ModalHeader>
                    <ModalBody>
                        <form>
                            <div className="form-group">
                                <label htmlFor="selectTagsExtractionMethod">Extraction&nbsp;Method&nbsp;</label>
                                <select className="form-control"
                                        id="selectTagsExtractionMethod"
                                        name="selectTagsExtractionMethod"
                                        onChange={(e) => this.onChangeTagExtractionMethod(e)}
                                        ref={(input) => this.selectTagsExtractionMethod = input} >
                                    <option value="">--SELECT  ONE--</option>
                                    <option value="KMEANS">By Clustering (KMEANS)</option>
                                    <option value="RAKE">By Keyword Extraction (RAKE)</option>
                                    <option value="BAYES">By Bayesian Naive Estimator (BAYES)</option>
                                </select>
                            </div>
                            <div className='form-group hidden kmean-control-group' >
                                <label htmlFor="selectTagsExtractionMethodClusters"># Clusters</label>
                                <select className="form-control" id="selectTagsExtractionMethodClusters"
                                        name="selectTagsExtractionMethodClusters"
                                        defaultValue={5}
                                        ref={(input) => this.selectTagsExtractionMethodClusters = input} >
                                    { cluster_options }
                                 </select>
                            </div>
                            <div className='form-group hidden bayes-control-group' >

                            </div>
                            <div className="checkbox">
                                <label>
                                    <input
                                        id="deleteFormerExtractedTags"
                                        name="deleteFormerExtractedTags"
                                        defaultChecked={true}
                                        type="checkbox" ref={(input) => this.deleteFormerExtractedTags = input}/> Delete Former Extracted Tags
                                </label>
                            </div>
                        </form>
                    </ModalBody>
                    <ModalFooter>
                        <button className='btn btn-default' onClick={(e) => this.hideModalExtractTags()}>
                            Close
                        </button>
                        <button className='btn btn-primary' onClick={(e) => this.onExtractTags()}>
                            Extract!
                        </button>
                    </ModalFooter>
                </Modal>


                <h3>Survey Free Text Answers</h3>
                <div className="row">
                    <div className="col-md-4">
                        <SurveyTemplateSelector className="form-control"
                                                items={this.props.templates}
                                                onChange={(e) => this.onChangeSurveyTemplate(e)}>

                        </SurveyTemplateSelector>
                    </div>
                    <div className="col-md-4">
                        <SurveyTemplateQuestionSelector className="form-control"
                                                        items={this.props.questions}
                                                        onChange={(e) => this.onChangeSurveyTemplateQuestion(e)}>
                        </SurveyTemplateQuestionSelector>
                    </div>
                    <div className="col-md-4">
                        <SurveyLanguageSelector items={this.props.languages}
                                                defaultValue={this.props.selected_languages}
                                                onChange={(e) => this.onChangeLanguage(e)}>
                        </SurveyLanguageSelector>
                    </div>
                </div>
                <div>

                </div>
                <div className="row filters-container hidden">
                    <div className="col-md-6">
                        <input type="text" className="form-control" onChange={(e) => this.onFilterByTag(e) } placeholder="Search By Tag" id="filterByTag"/>
                    </div>
                    <div className="col-md-6">
                        <button type="button"
                                onClick={(e) => this.onExportResultsClicked(e)}
                                className="btn btn-primary btn-sm btn-action">Export
                        </button>
                        <button type="button"
                                onClick={(e) => this.onExtractTagsClicked(e)}
                                className="btn btn-primary btn-sm btn-action">Extract Tags
                        </button>

                        <button type="button"
                                onClick={(e) => this.onShowStatsClicked(e)}
                                className="btn btn-primary btn-sm btn-action">Show Stats
                        </button>
                    </div>

                </div>
                <div className="row main-controls-row">
                    <div className="col-md-12">
                        <button className='btn btn-primary'
                                title="Train Bayesian Naive Model"
                                onClick={(e) => this.onRebuildBayesianNaiveModel(e)}>
                            Train Bayesian Naive Model
                        </button>
                    </div>
                </div>
                <table className="table">
                    <thead>
                    <tr>
                        <th>
                            Id
                        </th>
                        <th>
                            Value
                        </th>
                        <th>
                            Tags
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    {
                        this.props.answers.map
                        (
                            answer =>

                            <tr key={answer.id}>
                                <td>
                                    {answer.member_email ? (
                                        <a href={`mailto:${answer.member_email}`} title={`answered by ${answer.member_email}`}>
                                            {answer.id}
                                        </a>
                                    ) : (
                                        <span>{answer.id}</span>
                                    )}
                                </td>
                                <td className="answer-value-cell">
                                    <textarea className="answer-value-textarea" id={`answer_value_textarea_${answer.id}`} defaultValue={answer.value}>
                                    </textarea>
                                </td>
                                <td>
                                    <TagInput
                                    onTagRemoved={ (e) => this.onTagRemoved(e, answer.id) }
                                    onTagAdded={ (e) => this.onTagAdded(e, answer.id) }
                                    tags={answer.tags} ></TagInput>
                                </td>
                                <td>
                                    <button type="button"
                                            title="Update Answer Value"
                                            onClick={(e) => this.onUpdateAnswerValueClicked(e, answer.id)}
                                            className="btn btn-primary btn-sm">
                                        Update
                                    </button>
                                    {answer.survey_id > 0 &&
                                    <button type="button"
                                            title="View Survey"
                                            onClick={(e) => this.onViewSurveyClicked(e, answer.survey_id)}
                                            className="btn btn-default btn-sm"
                                            style={{marginLeft: '10px'}}
                                    >
                                        View
                                    </button>
                                    }
                                </td>
                            </tr>
                        )
                    }
                    </tbody>
                </table>
                <nav aria-label="Page navigation" className={(this.props.answers.length > 0 ? 'show' : 'hidden')}>
                    <select defaultValue={this.props.page_size} className="form-control page-size-control"
                            onChange={(e) => this.onChangePageSize(e)}
                            name="pagination_page_size">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="0">--ALL--</option>
                    </select>
                    <ul className="pagination">
                        {pages}
                    </ul>
                </nav>
            </div>
        );
    }
}

export default connect(
    state => {
        return {
            template_id           : state.template_id,
            question_id           : state.question_id,
            answers               : state.answers,
            questions             : state.questions,
            languages             : state.languages,
            selected_languages    : state.selected_languages,
            page_count            : state.page_count,
            loading               : state.loading,
            current_page          : state.current_page,
            page_size             : state.page_size,
            reload                : state.reload,
            open_modal_show_stats : state.open_modal_show_stats,
            tags                  : state.tags,
            search_term           : state.search_term,
        }
    },
    dispatch => ({
        fetchFreeTextAnswers (template_id, page = 1, page_size = 25, order = '', question_id = 0, search_term = '', languages = '') {
            return dispatch(fetchAnswersPage({ template_id, page, page_size, order, question_id, search_term, languages}));
        },
        exportFreeTextAnswers (template_id, question_id = 0, search_term = '', languages = '') {
            return dispatch(exportAnswers({ template_id, question_id, search_term, languages}));
        },
        fetchQuestions(template_id){
            return dispatch(fetchFreeTextQuestionByTemplate({ template_id }));
        },
        fetchLanguages(template_id, question_id){
            return dispatch(fetchLanguagesByQuestion({ template_id, question_id }));
        },
        addTag(tag, template_id, question_id, answer_id){
            let payload    = { tag };
            return dispatch(addTagToFreeTextAnswer({template_id, question_id, answer_id}, payload));
        },
        removeTag(tag, template_id, question_id, answer_id){
            let payload    = { tag };
            return dispatch(removeTagFromFreeTextAnswer({ template_id, question_id, answer_id }, payload));
        },
        calculateTagsByKMEANS(template_id, question_id, delete_former_tags = 1, clusters = 5){
            return dispatch(extractTagsByKMeans({template_id, question_id, delete_former_tags, clusters}));
        },
        calculateTagsByBayes(template_id, question_id,  delete_former_tags = 1){
            return dispatch(extractTagsByBayes({template_id, question_id, delete_former_tags}));
        },
        rebuildBayesianNaiveModel(){
            return dispatch(rebuildBayesianNaiveModel());
        },
        calculateTagsByRAKE(template_id, question_id, delete_former_tags = 1){
            return dispatch(extractTagsByRAKE({template_id, question_id, delete_former_tags}));
        },
        updateAnswerValue(template_id, question_id, answer_id, value){
            let payload    = { value };
            return dispatch(updateFreeTextAnswer({template_id, question_id, answer_id}, payload));
        },
        fetchTagsByQuestion(template_id, question_id){
            return dispatch(fetchFreeTextAnswersTagsByQuestion({ template_id, question_id }));
        },
        showStatsView(template_id, question_id){
            return dispatch(showFreeTextAnswersStatsView({ template_id, question_id }));
        }
    })
)(SangriaSurveyFreeTextAnswersListApp);
