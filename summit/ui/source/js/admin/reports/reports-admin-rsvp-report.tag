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

        <table class="table rsvp-table">
            <thead>
                <tr>
                    <th style="width:2%">#</th>
                    <th>Attendee</th>
                    <th>Date</th>
                    <th each={ header in headers }>{ header }</th>
                    <th style="width:15%">Emails Sent</th>
                </tr>
            </thead>
            <tbody>
                <tr each={ rsvp, i in rsvps }>
                    <td>{(i + 1)}</td>
                    <td><a href="{ parent.base_url }/attendees/{ rsvp.attendee.id }">{ rsvp.attendee.id }</a></td>
                    <td>{ rsvp.date }</td>
                    <td each={ label, value in rsvp.rsvp }>{ value }</td>
                    <td>
                        <div class="email-popover" each={ email, j in rsvp.emails } data-toggle="popover" data-placement="left" data-html="true" data-content={ email.body }>
                            * { email.subject }
                        </div>
                    </td>
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
        this.base_url        = opts.base_url;
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
                        $('#send-email').attr('disabled',false);
                    } else if (data.event_count > 1) {
                        self.events = data.data;
                        $('#send-email').attr('disabled',true);
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
                    $('[data-toggle="popover"]').popover();
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

        self.dispatcher.on(self.dispatcher.OPEN_EMAIL_MODAL_RSVP_REPORT,function() {
            var emails = self.rsvps.filter(a => !a.attendee.emailed ).map(r => r.attendee.email).join(',');
            $('#email-from').val('');
            $('#email-to').val(emails);
            $('#email-subject').val('');
            $('#email-message').val('');
        });

        self.dispatcher.on(self.dispatcher.POPULATE_ALL_EMAILS_RSVP_REPORT,function() {
            var emails = self.rsvps.map(r => r.attendee.email).join(',');
            $('#email-to').val(emails);
        });

        self.dispatcher.on(self.dispatcher.SEND_EMAIL_RSVP_REPORT,function() {
            var from = $('#email-from').val();
            var to = $('#email-to').val();
            var subject = $('#email-subject').val();
            var message = $('#email-message').val();

            $('body').ajax_loader();

            var data = {from: from, to: to, subject: subject, message: message, event_id: self.event.event_id};

            $.ajax({
                type: 'POST',
                url: 'api/v1/summits/'+self.summit_id+'/reports/rsvp_send_emails',
                data: JSON.stringify(data),
                contentType: "application/json; charset=utf-8",
                dataType: "json"
            }).done(function(data) {
                $('body').ajax_loader('stop');
                swal({
                    title: "Done!",
                    text: "email sent successfully",
                    type: "success"
                },
                function(){
                    self.getReport(1);
                });
            }).fail(function(jqXHR) {
                var responseCode = jqXHR.status;
                $('body').ajax_loader('stop');
                if(responseCode == 412) {
                    var response = $.parseJSON(jqXHR.responseText);
                    swal('Validation error', response.messages[0].message, 'warning');
                } else {
                    swal('Error', 'There was a problem sending the email, please contact admin.', 'warning');
                }
            });
        });

    </script>

</reports-admin-rsvp-report>