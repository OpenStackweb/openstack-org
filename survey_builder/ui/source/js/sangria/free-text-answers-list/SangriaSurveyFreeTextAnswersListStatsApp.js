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
import RC2 from 'react-chartjs2';
import { render } from 'react-dom';
import randomColor from 'randomcolor';
import {mergeTags} from './actions';
import Confirm from 'react-confirm-bootstrap';


class SangriaSurveyFreeTextAnswersListStatsApp extends React.Component {

    constructor(props) {
        super(props);
        let tags = props.data.tags;

        let random_colors = randomColor({
            count: tags.length,
            luminosity: 'bright',
            hue: 'random',
            format: 'rgbArray'
        });

        for(let i=0; i < random_colors.length; i++){
            let color = random_colors[i];
            tags[i].bgnd_color = `rgba(${color[0]}, ${color[1]}, ${color[2]}, 0.3)`;
            tags[i].brd_color = `rgba(${color[0]}, ${color[1]}, ${color[2]}, 1)`;
        }


        let options = {
            title: {
                display: false,
            },
            legend: {
                display: false,
            }
        }

        let chart_data = {
            labels: tags.map(t => t.name),
            datasets: [{
                data: tags.map(t => t.qty),
                backgroundColor: tags.map(t => t.bgnd_color),
                borderColor: tags.map(t => t.brd_color),
            }]
        };

        let answer_count = this.getAnswerCount(tags);

        this.state = {
            tags: tags,
            tag_answers: answer_count,
            question_title: props.data.question_title,
            chartData: chart_data,
            chartOptions: options,
            select_all: true,
            isOpenModalMergeTags: false
        }

        this.onMergeTags = this.onMergeTags.bind(this);
    }

    shouldComponentUpdate(nextProps, nextState) {
        if (this.state.replace_tag != nextState.replace_tag) return false;
        return true;
    }

    onTagClick(e, tag){
        e.preventDefault();
        let chart_data = this.state.chartData;
        let tags = this.state.tags;

        tags.find(t => t.id == tag.id).active = e.target.classList.contains('active');

        chart_data = this.getChartData(tags, chart_data);
        let answer_count = this.getAnswerCount(tags);


        this.setState({
            chartData: chart_data,
            tag_answers: answer_count
        });

        return false;
    }

    onSelectAllClick(e){
        e.preventDefault();
        let tags = this.state.tags;
        let select_all = !this.state.select_all;
        let chart_data = this.state.chartData;

        for (let i = 0; i < tags.length; i++) {
            tags[i].active = select_all;
        }

        chart_data = this.getChartData(tags, chart_data);
        let answer_count = this.getAnswerCount(tags);

        this.setState({
            chartData: chart_data,
            tags: tags,
            select_all: select_all,
            tag_answers: answer_count
        });

        return false;
    }

    getChartData(tags, chart_data) {
        chart_data.labels = tags.filter(t => t.active).map(t => t.name);
        chart_data.datasets[0].data = tags.filter(t => t.active).map(t => t.qty);
        chart_data.datasets[0].backgroundColor = tags.filter(t => t.active).map(t => t.bgnd_color);
        chart_data.datasets[0].borderColor = tags.filter(t => t.active).map(t => t.brd_color);
        return chart_data;
    }

    getAnswerCount(tags) {
        if (tags.length == 0) return 0;
        let answer_ids = tags.filter(t => t.active).map(t => t.answer_ids);
        let unique_answers = Array.from(new Set([].concat(...answer_ids)));
        return unique_answers.length;
    }

    onReplaceTagChange(e){
        let target = e.currentTarget;
        let val    = target.value;

        this.setState({
            replace_tag: val
        });
    }

    onMergeTags(e){
        let tags = this.state.tags.filter(t => t.active).map(t => t.id);
        let replace_tag = this.state.replace_tag;
        if(tags.length ==  0 || replace_tag == '') return;

        this.props.mergeTags(this.props.data.template_id, this.props.data.question_id, tags, replace_tag);
    }

    render() {
        return (
            <div className="row">
                <div className="col-md-6 left_col">
                    <h4 className="graph_title" >{this.state.question_title + ' ( ' + this.state.tag_answers + ' out of ' + this.props.data.total_answers + ' )'}</h4>
                    <div id="graph-box">
                        <RC2 data={this.state.chartData} type='pie' options={this.state.chartOptions} />
                    </div>
                </div>
                <div className="col-md-6 right_col">
                    <div id="tag-list">
                        <h4>Tag List (<a id="select-all" onClick={(e) => this.onSelectAllClick(e)}>{(this.state.select_all ? 'select none' : 'select all')}</a>)</h4>
                        {
                            this.state.tags.map(
                                tag =>
                                <button type="button" key={'tag_' + tag.id}
                                        className={"btn btn-default btn-sm " + (tag.active ? "active" : "")}
                                        data-toggle="button"
                                        onClick={(e) => this.onTagClick(e, tag)}
                                        style={{backgroundColor: tag.bgnd_color}}>
                                    {tag.name} ({tag.qty})
                                </button>
                            )
                        }
                    </div>
                    <hr/>
                    <h4>Merge Tags</h4>
                    <div id="merge-box" className="row">
                        <div className="col-md-8">
                            <input type="text" id="merge-tag" className="form-control"
                                onChange={(e) => this.onReplaceTagChange(e) }
                                placeholder="Replace with Tag"
                            />
                        </div>
                        <div className="col-md-4">
                            <Confirm
                                onConfirm={this.onMergeTags}
                                body="Are you sure you want to merge the selected tags?"
                                confirmText="Merge"
                                title="Merge Tags">
                                    <button type="button" className="btn btn-primary">
                                        Merge
                                    </button>
                            </Confirm>

                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default connect(
    state => {
    },
    dispatch => ({
        mergeTags(template_id, question_id, tags, replace_tag){
            let payload    = { tags, replace_tag };
            return dispatch(mergeTags({ template_id, question_id }, payload));
        }
    })
)(SangriaSurveyFreeTextAnswersListStatsApp);
