<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<reports-admin-presentations-by-track-report>
    <div class="report_filters">
        <div class="row">
            <div class="col-md-2">
                <label>Show Info</label>
                <select id="display_filter" multiple class="form-control">
                    <option value="speaker">Speakers</option>
                    <option value="owner">Owner</option>
                    <option value="moderator">Moderator</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>Status</label>
                <select id="status_filter" class="form-control filter">
                    <option value="">All</option>
                    <option value="Received">Received</option>
                    <option value="null">Null</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>Published</label>
                <select id="published_filter" class="form-control filter">
                    <option value="">All</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="not_scheduled">Not Scheduled</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>Track Filter</label>
                <select id="track_filter" multiple class="form-control filter">
                    <option value="{ track.id }" each={ track, i in tracks }>{ track.title }</option>
                </select>
            </div>
            <div class="col-md-2">
                <button id="apply_filters" class="btn btn-primary"> Filter </button>
            </div>
        </div>
    </div>

    <hr>
    <div class="report_count">
        <label>Track Count:</label>
        <div class="row">
            <div class="col-md-2" each={ track, track_count in track_count }>{ track } : { track_count }</div>
        </div>
    </div>
    <br>

    <div class="panel panel-default">
        <div class="panel-heading">Presentations ({ page_data.total })</div>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th class="sortable sorted" data-sort="title" data-dir="ASC">Presentation<i class="fa fa-caret-up"></i></th>
                    <th class="sortable" data-sort="track">Category</th>
                    <th class="sortable" data-sort="last_name" if={ showThis('speaker') }>Speaker</th>
                    <th if={ showThis('speaker') }>Email</th>
                    <th if={ showThis('moderator') }>Moderator</th>
                    <th if={ showThis('owner') }>Owner</th>
                    <th class="sortable" data-sort="company" if={ showThis('speaker') }>Org</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr each={ presentation, i in presentations }>
                    <td>{ presentation.id }</td>
                    <td>{ presentation.title }</td>
                    <td>{ presentation.track }</td>
                    <td if={ showThis('speaker') }>{ presentation.first_name } { presentation.last_name }</td>
                    <td if={ showThis('speaker') }>{ presentation.email }</td>
                    <td if={ showThis('moderator') }>{ presentation.moderator_email }</td>
                    <td if={ showThis('owner') }>{ presentation.owner_email }</td>
                    <td if={ showThis('speaker') }>{ presentation.company }</td>
                    <td>{ presentation.status }</td>
                </tr>
            </tbody>
        </table>
    </div>
    <nav>
    <ul id="report-pager" class="pagination"></ul>
    </nav>

    <script>
        this.dispatcher      = opts.dispatcher;
        this.page_data       = {total: 100, limit: opts.page_limit, page: 1};
        this.summit_id       = opts.summit_id;
        this.tracks          = opts.tracks;
        this.presentations   = [];
        this.track_count     = [];
        this.show_col        = [];
        var self             = this;


        this.on('mount', function() {
            self.getReport(1);

            $('.sortable').click(function(){
                self.parent.toggleSort($(this));
                self.getReport(self.page_data.page);
            });

            $('#apply_filters').click(function(){
                self.getReport(1);
            });

        });

        getReport(page) {
            $('body').ajax_loader();
            var sort = $('.sorted').data('sort');
            var sort_dir = $('.sorted').data('dir');
            var term = $('#search-term').val();
            var status = $('#status_filter').val();
            var published = $('#published_filter').val();
            var track = $('#track_filter').val();
            var show_col = $('#display_filter').val();

            $.getJSON('api/v1/summits/'+self.summit_id+'/reports/presentations_by_track_report',
                {page:page, items: self.page_data.limit, sort: sort, sort_dir: sort_dir, term: term,
                status: status, published: published, track: track, show_col: show_col },
                function(data){
                    self.presentations = data.data;
                    self.page_data.page = page;
                    self.page_data.total = parseInt(data.total);
                    self.track_count = data.track_count;
                    self.show_col = show_col;

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

        self.dispatcher.on(self.dispatcher.GET_PRESENTATIONS_BY_TRACK_REPORT,function() {
            self.getReport(1);
        });

        self.dispatcher.on(self.dispatcher.EXPORT_PRESENTATIONS_BY_TRACK_REPORT,function() {
            var sort = $('.sorted').data('sort');
            var sort_dir = $('.sorted').data('dir');
            var term = $('#search-term').val();
            var status = $('#status_filter').val();
            var published = $('#published_filter').val();
            var track = ($('#track_filter').val()) ? $('#track_filter').val() : '';
            var show_col = ($('#display_filter').val()) ? $('#display_filter').val() : '';

            var query_string = 'term='+term+'&sort='+sort+'&sort_dir='+sort_dir+'&status='+status+'&published='+published+'&track='+track+'&show_col='+show_col;

            window.open('api/v1/summits/'+self.summit_id+'/reports/export/presentations_by_track_report?'+query_string, '_blank');
        });

        showThis(column) {
            return ($.inArray(column,self.show_col) != -1);
        }

    </script>

</reports-admin-presentations-by-track-report>