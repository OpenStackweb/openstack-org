<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<reports-admin-speaker-report>

    <div class="panel panel-default">
        <div class="panel-heading">Speakers ({ page_data.total })</div>

        <table class="table">
            <thead>
                <tr>
                    <th>Speaker Id</th>
                    <th>Member Id</th>
                    <th class="sortable sorted" data-sort="name" data-dir="ASC">Speaker<i class="fa fa-caret-up"></i></th>
                    <th>Email</th>
                    <th>Phone On Site</th>
                    <th>Company</th>
                    <th>Presentation</th>
                    <th>Track</th>
                    <th class="center_text">Confirmed?</th>
                    <th data-sort="registered" data-dir="ASC" class="center_text sortable">Registered?</th>
                    <th data-sort="checked_in" data-dir="ASC" class="center_text sortable">Checked In?</th>
                </tr>
            </thead>
            <tbody>
                <tr each={ speaker, i in speakers }>
                    <td class="speaker-id">{ speaker.speaker_id }</td>
                    <td>{ speaker.member_id }</td>
                    <td>{ speaker.name }</td>
                    <td>{ speaker.email }</td>
                    <td><input type="text" class="phone" value={ speaker.phone } /></td>
                    <td>{ speaker.company }</td>
                    <td>{ speaker.presentation }</td>
                    <td>{ speaker.track }</td>
                    <td class="center_text"><i class={ fa: true, fa-check: speaker.confirmed, fa-times: !speaker.confirmed } ></i></td>
                    <td class="center_text"><input type="checkbox" class="registered" checked={ speaker.registered } /></td>
                    <td class="center_text"><input type="checkbox" class="checked_in" checked={ speaker.checked_in } /></td>
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
        this.speakers        = [];
        var self             = this;


        this.on('mount', function() {
            self.getReport(1);

            $('.sortable').click(function(){
                self.parent.toggleSort($(this));
                self.getReport(self.page_data.page);
            });

            $('.reports-wrapper').on('change','input',function(){
                $(this).parents('tr').addClass('changed');
            });
        });

        getReport(page) {
            $('body').ajax_loader();
            var sort = $('.sorted').data('sort');
            var sort_dir = $('.sorted').data('dir');

            $.getJSON('api/v1/summits/'+self.summit_id+'/reports/speaker_report',
                {page:page, items:self.page_data.limit, sort:sort, sort_dir:sort_dir},
                function(data){
                    self.speakers = data.data;
                    self.page_data.page = page;
                    self.page_data.total = parseInt(data.total);

                    var total_pages = (self.page_data.total) ? Math.ceil(self.page_data.total / self.page_data.limit) : 1;
                    var options = {
                        bootstrapMajorVersion:3,
                        currentPage: self.page_data.page ,
                        totalPages: total_pages,
                        numberOfPages: 10,
                        onPageChanged: function(e,oldPage,newPage){
                            self.parent.saveReport();
                            self.getReport(newPage);
                        }
                    }

                    $('#report-pager').bootstrapPaginator(options);

                    self.update();
                    $('body').ajax_loader('stop');
            });
        }

        self.dispatcher.on(self.dispatcher.SAVE_SPEAKER_REPORT,function(report) {
            var request = [];
            $('.changed').each(function(){
                var speaker_id = $('.speaker-id',this).text();
                var phone      = $('.phone',this).val();
                var registered = $('.registered',this).attr('checked') ? 1 : 0;
                var checked_in = $('.checked_in',this).attr('checked') ? 1 : 0;

                request.push({speaker_id: speaker_id, phone: phone, registered: registered, checked_in: checked_in});
            });

            if (request.length) {
                $.ajax({
                    type: 'PUT',
                    url: 'api/v1/summits/'+self.summit_id+'/reports/'+report,
                    data: JSON.stringify(request),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json"
                }).done(function(data) {
                    $('.changed').removeClass('changed');
                    swal('Updated', 'Changes saved.', 'success');
                }).fail(function(jqXHR) {
                    var responseCode = jqXHR.status;
                    if(responseCode == 412) {
                        var response = $.parseJSON(jqXHR.responseText);
                        swal('Validation error', response.messages[0].message, 'warning');
                    } else {
                        swal('Error', 'There was a problem saving the speaker, please contact admin.', 'warning');
                    }
                });
            }
        });

        self.dispatcher.on(self.dispatcher.EXPORT_SPEAKER_REPORT,function() {
            var sort     = $('.sorted').data('sort');
            var sort_dir = $('.sorted').data('dir');
            window.open('api/v1/summits/'+self.summit_id+'/reports/export/speaker_report?sort='+sort+'&sort_dir='+sort_dir, '_blank');
        });


    </script>

</reports-admin-speaker-report>