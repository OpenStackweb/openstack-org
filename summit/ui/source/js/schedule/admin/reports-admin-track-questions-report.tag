<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<reports-admin-track-questions-report>
    <div class="list-group" if={ track_count > 1 }>
        <a href="#" class="list-group-item" each={ track, i in tracks } onclick={ trackClick }>
            Track { track.track_id }: { track.title }
        </a>
    </div>
    <div class="panel panel-default" if={ track_count == 1 }>
        <div class="panel-heading">{ track.track_id } - <strong> { track.title } </strong></div>

        <table class="table">
            <thead>
                <tr>
                    <th each={ header in headers }>{ header }</th>
                </tr>
            </thead>
            <tbody>
                <tr each={ event, i in events }>
                    <td each={ label, value in event }>{ value }</td>
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
        this.events          = [];
        this.headers         = [];
        this.tracks          = [];
        this.track_id        = 0;
        this.track_count     = 0;
        var self             = this;


        this.on('mount', function() {
            self.getReport(1);
        });

        trackClick(ev) {
            $('#search-term').val(ev.item.track.track_id);
            self.getReport(1);
        }

        getReport(page) {
            $('body').ajax_loader();
            var term = $('#search-term').val();

            $.getJSON('api/v1/summits/'+self.summit_id+'/reports/track_questions_report',
                {page:page, items: self.page_data.limit, term: term},
                function(data){
                    if (data.track_count == 1) {
                        self.events = data.data;
                        self.track = data.track;
                        self.page_data.total = parseInt(data.total);
                        self.headers = data.headers;
                    } else if (data.track_count > 1) {
                        self.tracks = data.data;
                    }

                    self.track_count = data.track_count;
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
                    $('body').ajax_loader('stop');
            });
        }

        self.dispatcher.on(self.dispatcher.GET_TRACK_QUESTIONS_REPORT,function() {
            self.getReport(1);
        });

        self.dispatcher.on(self.dispatcher.EXPORT_TRACK_QUESTIONS_REPORT,function() {
            var term = $('#search-term').val();
            window.open('api/v1/summits/'+self.summit_id+'/reports/export/track_questions_report?term='+term, '_blank');
        });

    </script>

</reports-admin-track-questions-report>