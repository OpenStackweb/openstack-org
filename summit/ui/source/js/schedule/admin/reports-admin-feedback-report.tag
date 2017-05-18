<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<reports-admin-feedback-report>
    <div class="group_by_filter">
        <div class="row">
            <div class="col-md-2" style="width:auto;line-height: 33px;"> Group By </div>
            <div class="col-md-2" style="width:auto;">
                <select id="group-by" class="form-control">
                    <option value="feedback">Feedback</option>
                    <option value="track">Track</option>
                    <option value="presentation">Presentation</option>
                    <option value="speaker">Speaker</option>
                </select>
            </div>
        </div>
    </div>


    <div if={ is_grouped }>
        <h2>{ header.title }</h2>
        <div class="list-group">
            <a href="#" class="list-group-item" each={ feedback_group, i in feedbacks } onclick={ groupClick }>
                <div class="row">
                    <div class="col-md-4">{ feedback_group.grouped_item }</div>
                    <div class="col-md-4">
                        <input class="rating" value="{ feedback_group.avg_rate }"> { feedback_group.avg_rate }
                    </div>
                    <div class="col-md-4"> of total { feedback_group.feedback_count } </div>
                </div>
            </a>
        </div>
    </div>

    <div class="panel panel-default" if={ !is_grouped }>
        <div class="panel-heading">
            { header.title } -
            <input class="rating" value="{ header.avg_rate }"> { header.avg_rate }
            of { page_data.total } feedbacks
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Rate</th>
                    <th>Presentation</th>
                    <th>Speakers</th>
                    <th>Critic</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                <tr each={ feedback, i in feedbacks }>
                    <td><input class="rating" value="{ feedback.rate }"></td>
                    <td>{ feedback.title }</td>
                    <td>{ feedback.speakers }</td>
                    <td>{ feedback.first_name } { feedback.last_name }</td>
                    <td>{ feedback.note }</td>
                </tr>
            </tbody>
        </table>
    </div>
    <nav>
    <ul id="report-pager" class="pagination"></ul>
    </nav>

    <script>
        this.dispatcher      = opts.dispatcher;
        this.page_data       = {total: 1, limit: opts.page_limit, page: 1};
        this.summit_id       = opts.summit_id;
        this.feedbacks       = [];
        this.header          = [];
        this.is_grouped      = false;
        this.source          = '';
        this.source_id       = 0;
        var self             = this;


        this.on('mount', function() {
            self.getReport(1);

            $('#group-by').change(function(){
                self.getReport(1);
            });

        });

        groupClick(ev) {
            self.source = ev.item.feedback_group.source;
            self.source_id = ev.item.feedback_group.source_id;
            self.getReportBySource(1);
        }

        getReport(page) {
            $('body').ajax_loader();
            var term = $('#search-term').val();
            var group_by = $('#group-by').val();

            self.source = '';
            self.source_id = 0;

            $.getJSON('api/v1/summits/'+self.summit_id+'/reports/feedback_report',
                {page:page, items: self.page_data.limit, term: term, group_by: group_by},
                function(data){
                    self.feedbacks = data.data;
                    self.header = data.header;
                    self.is_grouped = data.is_grouped;
                    self.page_data.total = parseInt(data.total);
                    self.page_data.page = page;

                    var total_pages = (self.page_data.total) ? Math.ceil(self.page_data.total / self.page_data.limit) : 1;
                    var options = {
                        bootstrapMajorVersion:3,
                        currentPage: self.page_data.page ,
                        totalPages: total_pages,
                        numberOfPages: 10,
                        onPageChanged: function(e,oldPage,newPage){
                            self.getReport(newPage);
                        }
                    }

                    $('#report-pager').bootstrapPaginator(options);

                    self.update();

                    $('.rating').rating('refresh',{
                        disabled: true, showCaption:false, showClear:false, size: "xxxs", min:0, max:5, step:0.1
                    });

                    $('body').ajax_loader('stop');
            });
        }

        getReportBySource(page) {
            $('body').ajax_loader();

            $.getJSON('api/v1/summits/'+self.summit_id+'/reports/feedback_report/'+self.source+'/'+self.source_id,
                {page:page, items: self.page_data.limit},
                function(data){
                    self.feedbacks = data.data;
                    self.header = data.header;
                    self.is_grouped = data.is_grouped;
                    self.page_data.total = parseInt(data.total);
                    self.page_data.page = page;

                    var total_pages = (self.page_data.total) ? Math.ceil(self.page_data.total / self.page_data.limit) : 1;
                    var options = {
                        bootstrapMajorVersion:3,
                        currentPage: self.page_data.page ,
                        totalPages: total_pages,
                        numberOfPages: 10,
                        onPageChanged: function(e,oldPage,newPage){
                            self.getReportBySource(newPage);
                        }
                    }

                    $('#report-pager').bootstrapPaginator(options);

                    self.update();

                    $('.rating').rating('refresh',{
                        disabled: true, showCaption:false, showClear:false, size: "xxxs", min:0, max:5, step:0.1
                    });
    
                    $('body').ajax_loader('stop');
            });
        }

        self.dispatcher.on(self.dispatcher.GET_FEEDBACK_REPORT,function() {
            self.getReport(1);
        });

        self.dispatcher.on(self.dispatcher.EXPORT_FEEDBACK_REPORT,function() {
            var term = $('#search-term').val();
            var group_by = $('#group-by').val();

            var query_string = 'term='+term+'&group_by='+group_by;
            var url          = 'api/v1/summits/'+self.summit_id+'/reports/export/feedback_report';

            if (self.source && self.source_id) {
                url += '/'+self.source+'/'+self.source_id;
            }

            window.open(url+'?'+query_string, '_blank');
        });

    </script>

</reports-admin-feedback-report>