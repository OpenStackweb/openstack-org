<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<reports-admin-rsvp-report>
    <div if={ event_count == 0 }>
        No events match search.
    </div>
    <div class="list-group" if={ event_count > 1 }>
        <a href="#" class="list-group-item" each={ event, i in events } onclick={ eventClick }>
            Event { event.event_id }: { event.title } ({ event.date })
        </a>
    </div>
    <div class="panel panel-default" if={ event_count == 1 }>
        <div class="panel-heading">{ event.event_id } - <strong> { event.title } </strong> - { event.date } ({ page_data.total } attendees)</div>

        <table class="table">
            <thead>
                <tr>
                    <th each={ header in headers }>{ header }</th>
                </tr>
            </thead>
            <tbody>
                <tr each={ rsvp, i in rsvps }>
                    <td each={ label, value in rsvp }>{ value }</td>
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
        this.rsvps           = [];
        this.headers         = [];
        this.events          = [];
        this.event_id        = 0;
        this.event_count     = 0;
        var self             = this;


        this.on('mount', function() {
            self.getReport(1);
        });

        eventClick(ev) {
            $('#search-term').val(ev.item.event.event_id);
            self.getReport(1);
        }

        getReport(page) {
            $('body').ajax_loader();
            var term = $('#search-term').val();

            $.getJSON('api/v1/summits/'+self.summit_id+'/reports/rsvp_report',
                {page:page, items: self.page_data.limit, term: term},
                function(data){
                    if (data.event_count == 1) {
                        self.rsvps = data.data;
                        self.event = data.event;
                        self.page_data.total = parseInt(data.total);
                        self.headers = data.headers;
                    } else if (data.event_count > 1) {
                        self.events = data.data;
                    }

                    self.event_count = data.event_count;
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

        self.dispatcher.on(self.dispatcher.GET_RSVP_REPORT,function() {
            self.getReport(1);
        });

        self.dispatcher.on(self.dispatcher.EXPORT_RSVP_REPORT,function() {
            var term = $('#search-term').val();
            window.open('api/v1/summits/'+self.summit_id+'/reports/export/rsvp_report?term='+term, '_blank');
        });

    </script>

</reports-admin-rsvp-report>