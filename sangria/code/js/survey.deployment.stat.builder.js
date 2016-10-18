/**
 * Copyright 2015 OpenStack Foundation
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
$(document).ready(function() {
    renderGraph(pu_answers);
});

function renderGraph(values) {
    var data = [];
    var ticks = [];
    var series = [];

    for (var label in values) {
        series.push({label:label});
        var cat = values[label];
        var array_data = [];
        for (var sublabel in cat) {
            var first_word = sublabel.split(' ')[0];
            first_word = first_word.split('(')[0];
            if ($.inArray(first_word,ticks) < 0) {
                ticks.push(first_word);
            }
            array_data.push(cat[sublabel]);
        }
        data.push(array_data);
    }

    var plot1 = $.jqplot('projects_used_graph', data, {
        height: 400,
        stackSeries: true,
        legend: {
            show: true,
            placement: 'outsideGrid'
        },
        series: series,
        seriesDefaults:{
            renderer:$.jqplot.BarRenderer,
            rendererOptions: {fillToZero: true},
            pointLabels: {
                show: true,
                formatString: '%s%',
                hideZeros: true
            }
        },
        axesDefaults: {
            tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
            tickOptions: {
                angle: -90,
                fontSize: '8pt'
            }
        },
        axes: {
            xaxis: {
                renderer: $.jqplot.CategoryAxisRenderer,
                ticks: ticks
            },
            yaxis: {
                min:0,
                max:100,
                tickInterval: 10
            }
        },
        highlighter: { show: false }
    });
}

