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

let backgroundColor = [];
let borderColor = [];

let colors = randomColor({
    count: data.length,
    luminosity: 'bright',
    hue: 'random',
    format: 'rgbArray',
});

for(let color of colors){
    backgroundColor.push(`rgba(${color[0]}, ${color[1]}, ${color[2]}, 0.3)`);
    borderColor.push(`rgba(${color[0]}, ${color[1]}, ${color[2]}, 1)`);
}

const chartData = {
    labels: labels,
    datasets: [{
        data: data,
        backgroundColor: backgroundColor,
        borderColor:borderColor,
    }]
};

class SangriaSurveyFreeTextAnswersListStatsApp extends React.Component {
    render() {
        return (
            <RC2 data={this.props.chartData} type='pie' options={this.props.chartOptions} />
        );
    }
}

document.addEventListener('DOMContentLoaded', function init() {
    if (document.getElementById(containerId)) {
        render(
            <SangriaSurveyFreeTextAnswersListStatsApp chartData={chartData} chartOptions={options}/>
            ,
            document.getElementById(containerId)
        );
    }
});