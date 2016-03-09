<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<reports-admin-speaker-report>

    <div class="panel panel-default">
        <div class="panel-heading">Speakers ({ page_data.total })</div>

        <table id="report-table" class="table">
            <thead>
                <tr>
                    <th>Speaker Id</th>
                    <th>Member Id</th>
                    <th class="sortable" data-sort="name">FullName<i class="fa fa-caret-up"></i></th>
                    <th>Email</th>
                    <th>Phone On Site</th>
                    <th>Company</th>
                    <th>Presentation</th>
                    <th>Track</th>
                    <th class="center_text">Confirmed?</th>
                    <th data-sort="RegisteredForSummit" class="center_text sortable">Registered?</th>
                    <th data-sort="CheckedIn" class="center_text sortable">Checked In?</th>
                </tr>
            </thead>
            <tbody>
                <tr each={ speaker, i in speakers }>
                    <td>{ speaker.speaker_id }</td>
                    <td>{ speaker.member_id }</td>
                    <td>{ speaker.name }</td>
                    <td>{ speaker.email }</td>
                    <td>{ speaker.phone }</td>
                    <td>{ speaker.company }</td>
                    <td>{ speaker.presentation }</td>
                    <td>{ speaker.track }</td>
                    <td class="center_text"><i class={ fa: true, fa-check: speaker.confirmed, fa-times: !speaker.confirmed } ></i></td>
                    <td class="center_text"><i class={ fa: true, fa-check: speaker.registered, fa-times: !speaker.registered } ></i></td>
                    <td class="center_text"><input type="checkbox" id="checked_in_{ speaker.id }" checked={ speaker.checked_in } onchange={ parent.saveRow } /></td>
                </tr>
            </tbody>
        </table>
    </div>
    <nav>
    <ul id="report-pager" class="pagination"></ul>
    </nav>

    <script>
        this.page_data       = {total: 100, limit: opts.page_limit, page: 1};
        this.summit_id       = opts.summit_id;
        this.sort            = 'name';
        this.sort_dir        = 'ASC';
        this.speakers        = [];
        var self             = this;


        this.on('mount', function() {
            self.getReport(1);

            $('th').click(function(){
                self.sort = $(this).data('sort');
                self.sort_dir = $(this).has('.fa-caret-down').length ? 'ASC' : 'DESC';
                $('.fa','th').remove();
                var arrow = (self.sort_dir == 'ASC') ? '<i class="fa fa-caret-up"></i>' : '<i class="fa fa-caret-down"></i>';

                $(this).html($(this).text()+arrow);

                self.getReport(self.page_data.page);

            });
        });

        getReport(page) {
            $('body').ajax_loader();

            $.getJSON('api/v1/summits/'+self.summit_id+'/reports/speaker_report',
                {page:page, items:self.page_data.limit, sort:self.sort, sort_dir:self.sort_dir},
                function(data){
                    self.speakers = data.data;
                    self.page_data.page = page;
                    self.page_data.total = data.total;

                    var total_pages = Math.ceil(self.page_data.total / self.page_data.limit);
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

        saveRow(e) {
            var assistance_id = e.item.speaker.id;
            var request = {
                checked_in: ($('#checked_in_'+assistance_id).attr('checked') ? 1 : 0),
            };

            $.ajax({
                type: 'PUT',
                url: 'api/v1/summits/'+self.summit_id+'/reports/'+assistance_id,
                data: JSON.stringify(request),
                contentType: "application/json; charset=utf-8",
                dataType: "json"
            }).done(function(data) {

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

    </script>

</reports-admin-speaker-report>