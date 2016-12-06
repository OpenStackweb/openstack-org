<raw>
    <span></span>
    this.on('update', function(){
        if(opts.content) {
            this.root.innerHTML = opts.content
        }
    })
</raw>
<survey-report-dashboard>
    <h2>{ section.Name }</h2>
    <raw class="section_desc" content="{ section.Description }"/>
    <div id="dashboard">
        <div class="graph_box { question.Graph }" each="{ question in section.Questions }">
            <div class="graph_title">
                <raw content="{ question.Title }"/>
            </div>
            <div if={ question.Total > 0 }>
                <div id="graph_{ question.ID }" class="graph"></div>
                <span class="label_extra" if={ question.ExtraLabel }>{ question.ExtraLabel }</span>
                <span class="label_n">n={ question.Total }</span>
                <div class="clearfix"></div>
            </div>
            <div if={ question.Total == 0 }>
                There is no data available for a sample size this small.
                (<a href="" onclick="return false" data-toggle="tooltip" data-placement="top" title="We require a minimum of 10 responses before we reveal a sample set in order to provide a degree of anonymity for the survey participants.">?</a>)
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

    <script>
        this.section    = null;
        this.dispatcher = opts.dispatcher;
        this.api        = opts.api;
        var self        = this;

        this.on('updated', function(){
            if (self.section) {
                for (var key in self.section.Questions) {
                    var values =  self.section.Questions[key].Values;
                    var graph_type = self.section.Questions[key].Graph;
                    if (Object.keys(values).length) {
                        self.renderGraph('graph_'+self.section.Questions[key].ID, values, graph_type);
                    }
                }
            }

            $('[data-toggle="tooltip"]').tooltip();
        });

        self.api.on(self.api.REPORT_RETRIEVED, function(data)
        {
            self.section = data;
            self.update();
            $('body').ajax_loader('stop');

        });

        self.api.on(self.api.CLEAR_REPORT, function()
        {
            self.section = [];
            self.update();
            $('body').ajax_loader('stop');

        });

        renderGraph(graph_id, values, graph_type) {
            var graph_data = [];

            switch (graph_type) {
                case 'pie':
                    for (var label in values) {
                        graph_data.push([label, values[label]])
                    }

                    var plot1 = $.jqplot(graph_id, [graph_data], {
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

                    var plot1 = $.jqplot(graph_id, data, {
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

                    var plot1 = $.jqplot(graph_id, [data], {
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
        }

        self.dispatcher.on(self.dispatcher.EXPORT_TO_PDF, function() {
            var XHRs = [];
            var doc = new jsPDF("p","mm","a4");

            doc.setFont("times","normal");
            doc.setTextColor(42, 78, 104);
            doc.setDrawColor(42, 78, 104);

            doc.setFontSize(22);
            doc.text(25, 30, self.section.Name);
            doc.line(25, 35, 185, 35);

            doc.setFontSize(14);
            var desc = $('.section_desc').text();
            var split_desc = (desc.length > 60) ? doc.splitTextToSize(desc, 160) : desc;
            doc.text(25, 45, split_desc );

            var graph_count = $('.graph_box').length;
            var pos_x,pos_y,height,width,ratio,page_height,page_width;

            $('.graph_box').each(function(idx,element){
                html2canvas($(element), {
                    onrendered: function(canvas) {
                        page_height = doc.internal.pageSize.height;
                        page_width = doc.internal.pageSize.width;
                        pos_x = 25;
                        pos_y = (graph_count == $('.graph_box').length) ? 50 : pos_y + height + 10;
                        var page_width_wom = page_width - (pos_x * 2); // page width without margins
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
                        var imgData = canvas.toDataURL("image/jpeg",1.0);
                        doc.addImage(imgData, 'JPEG', pos_x, pos_y, width, height);
                        if (!--graph_count) doc.save(self.section.Name+'.pdf');
                    },
                });
            });



        });

    </script>
</survey-report-dashboard>