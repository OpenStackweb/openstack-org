<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<attendee-list>

    <div class="row">
        <div class="col-md-6" style="margin:0  0 20px 0;">
            <div class="input-group" style="width: 100%;">
                <input data-rule-required="true" data-rule-minlength="3" type="text" id="attendees_search_term" class="form-control input-global-search" placeholder="Search by Name">
                <span class="input-group-btn" style="width: 5%;">
                    <button class="btn btn-default btn-global-search" id="search_attendees"><i class="fa fa-search"></i></button>
                    <button class="btn btn-default btn-global-search-clear" onclick={ clearClicked }>
                        <i class="fa fa-times"></i>
                    </button>
                </span>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Attendees ({ page_data.total_items })</div>

        <table id="attendees-table" class="table">
            <thead>
                <tr>
                    <th>Member Id</th>
                    <th>FullName</th>
                    <th>Email</th>
                    <th>Bought Date</th>
                    <th>Checked In?</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <tr each={ attendee, i in attendees }>
                    <td>{ attendee.member_id }</td>
                    <td>{ attendee.name }</td>
                    <td>{ attendee.email }</td>
                    <td>{ attendee.ticket_bought }</td>
                    <td>{ attendee.checked_in }</td>
                    <td>
                        <a href="{ attendee.link }" class="btn btn-default btn-sm" role="button">Edit</a>
                        <button class="btn btn-success btn-sm" onclick={ openSchedule }>Schedule</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="modal fade" id="schedule_modal">
            <div class="modal-dialog  modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title"><span id="modal_atte_name"></span> Schedule</h4>
                    </div>
                    <div class="modal-body" id="modal_body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <nav>
    <ul id="attendees-pager" class="pagination"></ul>
    </nav>

    <script>
        this.attendees       = opts.attendees;
        this.page_data       = opts.page_data;
        this.summit_id       = opts.summit_id;
        var self             = this;

        var total_pages = Math.ceil(self.page_data.total_items / self.page_data.limit);

        this.on('mount', function() {
            var options = {
                bootstrapMajorVersion:3,
                currentPage: self.page_data.page ,
                totalPages: total_pages,
                numberOfPages: 10,
                onPageChanged: function(e,oldPage,newPage){
                    self.getAttendees(newPage,search_term);
                }
            }

            $('#attendees-pager').bootstrapPaginator(options);

            $('#search_attendees').click(function(e) {
                var search_term = $('#attendees_search_term').val();
                self.getAttendees(1,search_term);
            });

            $("#attendees_search_term").keydown(function (e) {
                if (e.keyCode == 13) {
                    $('#search_attendees').click();
                }
            });
        });

        getAttendees(page,search_term) {
            $('body').ajax_loader();

            $.getJSON('api/v1/summits/'+self.summit_id+'/attendees',{page:page, items:self.page_data.limit, term: search_term},function(data){
                self.attendees = data;
                self.page_data.page = page;
                self.update();
                $('body').ajax_loader('stop');
            });
        }

        clearClicked(e){
            $('#attendees_search_term').val('');
            self.getAttendees(1,'');
        }

        openSchedule(e){
            var attendee_id = e.item.attendee.id;
            $.getJSON('api/v1/summits/'+self.summit_id+'/attendees/'+attendee_id+'/schedule',{},function(data){
                var schedule_html = '<table class="table"><thead><tr><th>Event</th><th>Place</th><th>Time</th></thead><tbody>';
                $.each(data,function(idx,val){
                    schedule_html += '<tr><td>'+val.title+'</td>';
                    schedule_html += '<td>'+val.location+'</td>';
                    schedule_html += '<td>'+val.time+'</td></tr>';
                })
                schedule_html += '</tbody></table>';

                $('#modal_body').html(schedule_html);
                $('#modal_atte_name').html(e.item.attendee.name);

                $('#schedule_modal').modal("show");

            });
        }

    </script>

</attendee-list>