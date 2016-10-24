<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<attendee-eventbrite-list>

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
        <div class="panel-heading">Orphan Attendees ({ page_data.total_items })</div>

        <table id="attendees-table" class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Paid?</th>
                    <th>EventBrite Attendee ID</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <tr each={ attendee, i in attendees }>
                    <td>{ attendee.name }</td>
                    <td>{ attendee.email }</td>
                    <td>{ parent.hasPaid(attendee) }</td>
                    <td>{ attendee.eventbrite_id }</td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick={ openMatchModal }>Match</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="modal fade" id="match_attendee_modal">
            <div class="modal-dialog  modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4>Match Attendee</h4>
                    </div>
                    <div class="modal-body">
                        <div id="match_attendee_suggestions">

                        </div>
                        <label for="match_attendee_member">Member</label><br>
                        <input id="match_attendee_member" style="width:100%" />
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default btn-default pull-right" onclick="matchAttendee()">
                            Match
                        </button>
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
                    var search_term = $('#attendees_search_term').val();
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
                self.attendees = data.attendees;
                self.page_data.page = page;
                self.page_data.total_items = data.count;

                var total_pages = (data.count > 0) ? Math.ceil(self.page_data.total_items / self.page_data.limit) : 1;

                var options = {
                    currentPage: page ,
                    totalPages: total_pages,
                    numberOfPages: 10
                }

                $('#attendees-pager').bootstrapPaginator(options);

                self.update();
                $('body').ajax_loader('stop');
            });
        }

        clearClicked(e){
            $('#attendees_search_term').val('');
            self.getAttendees(1,'');
        }

        hasPaid(attendee){
            return (attendee.paid_amount == 0) ? 'No' : 'Yes';
        }

        openMatchModal(e){
            var attendee_id = e.item.attendee.eventbrite_id;
            var suggestions_html = '';

            $.each(e.item.attendee.suggestions, function(val,idx) {
                suggestions_html += '<div class="radio">';
                suggestions_html += '<label><input type="radio" name="match_suggestion">'+val.email +'('+val.reason+')</label>';
                suggestions_html += '</div>';
            });

            $('#match_attendee_suggestions').html(suggestions_html);
            $('#schedule_modal').modal("show");
        }

        matchAttendee(e) {

        }

    </script>

</attendee-eventbrite-list>