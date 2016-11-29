<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<reports-admin-presentations-by-track-report>

    <div class="panel panel-default">
        <div class="panel-heading">Speakers ({ page_data.total })</div>

        <table class="table">
            <thead>
                <tr>
                    <th>Url</th>
                    <th class="sortable" data-sort="title" data-dir="ASC">Presentation</th>
                    <th>Description</th>
                    <th class="sortable sorted" data-sort="track" data-dir="ASC">Track<i class="fa fa-caret-up"></i></th>
                    <th>FirstName</th>
                    <th>LastName</th>
                    <th>Email</th>
                    <th>Company</th>
                </tr>
            </thead>
            <tbody>
                <tr each={ presentation, i in presentations }>
                    <td>{ presentation.url }</td>
                    <td>{ presentation.title }</td>
                    <td style="width: 40%;">
                        <raw content="{ presentation.description }"/>
                    </td>
                    <td>{ presentation.track }</td>
                    <td>{ presentation.first_name }</td>
                    <td>{ presentation.last_name }</td>
                    <td>{ presentation.email }</td>
                    <td>{ presentation.company }</td>
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
        this.presentations   = [];
        var self             = this;


        this.on('mount', function() {
            self.getReport(1);

            $('.sortable').click(function(){
                self.parent.toggleSort($(this));
                self.getReport(self.page_data.page);
            });

        });

        getReport(page) {
            $('body').ajax_loader();
            var sort = $('.sorted').data('sort');
            var sort_dir = $('.sorted').data('dir');
            var term = $('#search-term').val();

            $.getJSON('api/v1/summits/'+self.summit_id+'/reports/presentations_company_report',
                {page:page, items: self.page_data.limit, sort: sort, sort_dir: sort_dir, term: term},
                function(data){
                    self.presentations = data.data;
                    self.page_data.page = page;
                    self.page_data.total = parseInt(data.total);

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

        self.dispatcher.on(self.dispatcher.GET_PRESENTATIONS_COMPANY_REPORT,function() {
            self.getReport(1);
        });

        self.dispatcher.on(self.dispatcher.EXPORT_PRESENTATIONS_COMPANY_REPORT,function() {
            var sort     = $('.sorted').data('sort');
            var sort_dir = $('.sorted').data('dir');
            var term = $('#search-term').val();
            window.open('api/v1/summits/'+self.summit_id+'/reports/export/presentations_company_report?term='+term+'&sort='+sort+'&sort_dir='+sort_dir, '_blank');
        });

    </script>

</reports-admin-presentations-by-track-report>