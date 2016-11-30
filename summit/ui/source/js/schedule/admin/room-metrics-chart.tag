<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<room-metrics-chart>

    <div id="chart{ event.id }" style="height:300px; width:500px;"></div>

    <script>
        this.event   = opts.event;
        this.metrics = [];
        this.options = null;
        this.plot    = null;
        var self     = this;
        var interval = 20000;


        this.on('mount', function() {
            self.getMetrics();
        });



        getMetrics() {
            $.getJSON('api/v1/summits/'+self.summit_id+'/reports/room_metrics/'+self.event.id, {}, function(data){
                if (!$.isEmptyObject(data)) {
                    self.type = data.Persons.type;
                    self.units = data.Persons.units;
                    self.metrics = data.Persons.metrics;
                    self.generatePlot();
                    setTimeout(self.doUpdate, interval);
                }
            });
        }

        generatePlot() {
            self.options = {
                title: self.event.title,
                axesDefaults: {
                    labelRenderer: $.jqplot.CanvasAxisLabelRenderer
                },
                seriesDefaults: {
                    rendererOptions: {
                        smooth: true
                    }
                },
                axes: {
                    xaxis: {
                        label: "Time",
                        numberTicks: self.metrics.length,
                        renderer:$.jqplot.DateAxisRenderer,
                        tickOptions:{formatString:'%#I:%M%p'},
                        min : self.event.start_time,
                        max: self.event.end_time,
                        numberTicks:10
                    },
                    yaxis: {
                        label: "People",
                        numberTicks: 11,
                        min : 0,
                        max: Math.ceil(self.event.capacity / 10) * 10
                    }
                },
                highlighter: {
                    show: true,
                    sizeAdjust: 7.5,
                    tooltipAxes: 'y'
                },
                cursor: {
                    show: false
                }
            }

            self.plot = $.jqplot ('chart'+self.event.id, [self.metrics], self.options);
        }

        doUpdate() {
            var last_timestamp = self.metrics[self.metrics.length-1][0];

            $.getJSON('api/v1/summits/'+self.summit_id+'/reports/room_metrics/'+self.event.id, {offset:last_timestamp}, function(data){
                if (!$.isEmptyObject(data)) {
                    if(self.metrics.length == 10){
                        self.metrics.shift();
                    }

                    self.metrics = self.metrics.concat(data.Persons.metrics);
                    if (self.plot) {
                        self.plot.destroy();
                    }

                    self.plot.series[0].data = self.metrics;
                    self.options.axes.xaxis.min = self.metrics[0][0];
                    self.options.axes.xaxis.max = self.metrics[self.metrics.length-1][0];
                    self.plot = $.jqplot ('chart'+self.event.id, [self.metrics],self.options);
                }
                setTimeout(self.doUpdate, interval);
            });
        }


    </script>

</room-metrics-chart>