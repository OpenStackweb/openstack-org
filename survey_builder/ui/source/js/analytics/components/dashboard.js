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
import { RawHTML } from '~core-components/rawhtml';

class SurveyAnalyticsDashboard extends React.Component {

    constructor(props) {
        super(props);
        this.plots = [];
    }

    componentDidUpdate(){
        console.log('componentDidUpdate');
        this.destroy();
        this.renderGraphs();
    }

    componentWillUnmount(){
        console.log('componentWillUnmount');
        this.destroy();
    }

    componentDidMount() {
       console.log('componentDidMount');
       this.renderGraphs();
    }

    destroy(){
        this.plots.map((plot, idx) => {
            plot.destroy();
        });
    }

    renderGraphs(){

        let { report } = this.props;

        if (report) {
            for (var key in report.Questions) {
                var values     =  report.Questions[key].Values;
                var graph_type = report.Questions[key].Graph;
                if ( values && Object.keys(values).length && $('#graph_'+report.Questions[key].ID).length > 0) {
                    this.renderGraph('graph_'+report.Questions[key].ID, values, graph_type);
                }
            }
        }

        $('[data-toggle="tooltip"]').tooltip();
    }

    renderGraph(graph_id, values, graph_type) {
        let graph_data = [];
        let plot1 = null;
        switch (graph_type) {
            case 'pie':
                for (var label in values) {
                    graph_data.push([label, values[label]])
                }

                plot1 = $.jqplot(graph_id, [graph_data], {
                    gridPadding: {top:30, bottom:0, left:0, right:0},
                    seriesDefaults:{
                        renderer:$.jqplot.PieRenderer,
                        trendline:{ show:false },
                        rendererOptions: { padding: 8, showDataLabels: true, startAngle: -90 }
                    },
                    legend:{
                        show:true
                    },
                    grid:{borderColor:'transparent',shadow:false,drawBorder:false}
                });
                break;
            case 'multibars':
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

                plot1 = $.jqplot(graph_id, data, {
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
                break;
            case 'bars':
                var data = [];
                var ticks = [];
                var series = [];

                for (var label in values) {
                    var first_word = label.split('(')[0];
                    ticks.push(first_word);
                    data.push(values[label]);
                }

                plot1 = $.jqplot(graph_id, [data], {
                    seriesDefaults:{
                        renderer:$.jqplot.BarRenderer,
                        rendererOptions: {fillToZero: true},
                        pointLabels: {
                            show: true,
                            formatString: '%s%'
                        }
                    },
                    axesDefaults: {
                        tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
                        tickOptions: {
                            angle: -30,
                            fontSize: '8pt'
                        }
                    },
                    axes: {
                        xaxis: {
                            renderer: $.jqplot.CategoryAxisRenderer,
                            ticks: ticks
                        }
                    },
                    highlighter: { show: false }
                });
                break;
        }
        if(plot1)
            this.plots = [...this.plots, plot1];

    }

    render() {
        let { report } = this.props;
        return(
          <div>
              <h2>{ report.Name }</h2>
              {report.Description &&
                <RawHTML className="section_desc">{report.Description}</RawHTML>
              }
              <div id="dashboard">
                  { report.Questions.map((question, index) => (
                      <div key={index} className={"graph_box " + question.Graph}>
                          <div className="graph_title">
                              {question.Title &&
                                <RawHTML>{question.Title}</RawHTML>
                              }
                          </div>
                          {( question.Total > 0) &&
                              <div>
                                  <div id={"graph_"+ question.ID } className="graph"></div>
                                  {question.ExtraLabel &&
                                    <span className="label_extra">{question.ExtraLabel}</span>
                                  }
                                  <span className="label_n">n={question.Total}</span>
                                  <div className="clearfix"></div>
                              </div>
                          }
                          {( question.Total == 0) &&
                              <div>
                                  There is no data available for a sample size this small.
                                  <a href="#" onClick={(e) => false}
                                     data-toggle="tooltip"
                                     data-placement="top"
                                     title="We require a minimum of 10 responses before we reveal a sample set in order to provide a degree of anonymity for the survey participants.">?</a>
                              </div>
                          }
                      </div>
                  ))}
                  <div className="clearfix"></div>
              </div>
          </div>
        );
    }
}

export default SurveyAnalyticsDashboard;