<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<room-metrics-chart>

    <div id="chart{ event.id }" style="height:300px; width:500px;"></div>

    <script>
        this.event   = opts.event;
        this.metrics = [];
        var self     = this;


        this.on('mount', function() {
            self.getMetrics();
    });



        getMetrics() {
            $.getJSON('api/v1/summits/'+self.summit_id+'/reports/room_metrics/'+self.event.id, {}, function(data){
                self.type = data.Persons.type;
                self.units = data.Persons.units;
                self.metrics = data.Persons.metrics;

    var plot2 = $.jqplot ('chart'+self.event.id, [self.metrics], {
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
    pad: 0
    },
    yaxis: {
    label: "People"
    }
    }
    });

            });
        }


    </script>

</room-metrics-chart>