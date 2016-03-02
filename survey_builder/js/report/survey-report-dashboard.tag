<survey-report-dashboard>
    <h2>{ section.Name }</h2>
    <p>{ section.Description }</p>
    <div id="dashboard">
        <div class="graph_box { question.Graph }" each="{ question in section.Questions }">
            <div class="graph_title">{ question.Title }</div>
            <div id="graph_{ question.ID }" class="graph"></div>
            <span class="label_n">n={ question.Total }</span>
            <div id="image_graph_{ question.ID }"></div>
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
                    self.renderGraph('graph_'+self.section.Questions[key].ID, values, graph_type);
                }
            }
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
            for (var label in values) {
                graph_data.push([label, values[label]])
            }

            var plot1 = $.jqplot(graph_id, [graph_data], {
                gridPadding: {top:30, bottom:0, left:0, right:0},
                seriesDefaults:{
                    renderer:self.getGraphRenderer(graph_type),
                    trendline:{ show:false },
                    rendererOptions: { padding: 8, showDataLabels: true }
                },
                legend:{
                    show:true
                },
                grid:{borderColor:'transparent',shadow:false,drawBorder:false}
            });

        }

        getGraphRenderer(graph_type) {
            switch(graph_type) {
                case 'pie':
                    return $.jqplot.PieRenderer;
                    break;
                case 'bar':
                    return $.jqplot.BarRenderer;
                    break;
                default :
                    return $.jqplot.BarRenderer;
                    break;
            }
        }

        self.dispatcher.on(self.dispatcher.EXPORT_TO_PDF, function() {
            var XHRs = [];
            var doc = new jsPDF("p","mm","a4");

            doc.setFont("times","normal");
            doc.setTextColor(42, 78, 104);

            doc.setFontSize(22);
            doc.text(25, 35, self.section.Name);

            doc.setFontSize(14);
            var split_desc = doc.splitTextToSize(self.section.Description, 160);
            doc.text(25, 45, split_desc );


            html2canvas($("#dashboard"), {
                onrendered: function(canvas) {
                    var imgData = canvas.toDataURL("image/jpeg",1.0);
                    var height = Math.round($("#dashboard").outerHeight() * 0.26);
                    var width = Math.round($("#dashboard").outerWidth() * 0.26);
                    var ratio = 160 / width;
                    height = Math.round(height * ratio);
                    doc.addImage(imgData, 'JPEG', 25, 90, 160, height);
                    doc.save(self.section.Name+'.pdf');
                },
            });



        });

    </script>
</survey-report-dashboard>