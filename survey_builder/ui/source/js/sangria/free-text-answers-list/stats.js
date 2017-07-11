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
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap';
import './style.stats.less'
import React from 'react';
import RC2 from 'react-chartjs2';
import { render } from 'react-dom';
import randomColor from 'randomcolor';
const containerId = 'openstack-sangria-survey-free-text-answerss-stats-app';

let random_backgrounds = [];
let random_borders = [];

let random_colors = randomColor({
    count: tags.length,
    luminosity: 'bright',
    hue: 'random',
    format: 'rgbArray',
});

for(let color of random_colors){
    random_backgrounds.push(`rgba(${color[0]}, ${color[1]}, ${color[2]}, 0.3)`);
    random_borders.push(`rgba(${color[0]}, ${color[1]}, ${color[2]}, 1)`);
}

let graph_colors = {background: random_backgrounds, border: random_borders};

const options = {
    title: {
        display: true,
        text: '{$QuestionTitle}'
    }
}



class SangriaSurveyFreeTextAnswersListStatsApp extends React.Component {

    constructor(props) {
        super(props);

        let options = {
            title: {
                display: true,
                text: props.question
            }
        }

        let chart_data = {
            labels: tags.sort((a,b) => +a.qty-+b.qty).map(t => t.name),
            datasets: [{
                data: tags.sort((a,b) => +a.qty-+b.qty).map(t => t.qty),
                backgroundColor: props.colors.background,
                borderColor: props.colors.border,
            }]
        };

        this.state = {
            tags: tags,
            chartData: chart_data,
            chartOptions: options,
            select_all: true
        }
    }

    onTagClick(e, tag){
        let chart_data = this.state.chartData;
        let tags = this.state.tags;

        tags.find(t => t.id == tag.id).active = e.target.classList.contains('active');

        chart_data.labels = tags.filter(t => t.active).sort((a,b) => +a.qty-+b.qty).map(t => t.name);
        chart_data.datasets[0].data = tags.filter(t => t.active).sort((a,b) => +a.qty-+b.qty).map(t => t.qty);

        this.setState({
            chartData: chart_data
        });
    }

    onSelectAllClick(e){
        let tags = this.state.tags;
        let select_all = !this.state.select_all;
        let chart_data = this.state.chartData;

        for (let i = 0; i < tags.length; i++) {
            tags[i].active = select_all;
        }

        chart_data.labels = tags.filter(t => t.active).sort((a,b) => +a.qty-+b.qty).map(t => t.name);
        chart_data.datasets[0].data = tags.filter(t => t.active).sort((a,b) => +a.qty-+b.qty).map(t => t.qty);

        this.setState({
            chartData: chart_data,
            tags: tags,
            select_all: select_all
        });
    }

    render() {
        return (
            <div>
                <RC2 data={this.state.chartData} type='pie' options={this.state.chartOptions} />
                <div id="tag-list">
                    <div>Tag List (<a onClick={(e) => this.onSelectAllClick(e)}>{(this.state.select_all ? 'select none' : 'select all')}</a>)</div>
                    <hr/>
                    {
                        this.state.tags.map(
                            tag =>
                            <button type="button"
                                    className={"btn btn-default btn-sm " + (tag.active ? "active" : "")}
                                    data-toggle="button"
                                    onClick={(e) => this.onTagClick(e, tag)}>
                                {tag.name}
                            </button>
                        )
                    }
                </div>
            </div>
        );
    }
}

document.addEventListener('DOMContentLoaded', function init() {
    if (document.getElementById(containerId)) {
        render(
            <SangriaSurveyFreeTextAnswersListStatsApp question={question_title} colors={graph_colors} tags={tags}/>
            ,
            document.getElementById(containerId)
        );
    }
});