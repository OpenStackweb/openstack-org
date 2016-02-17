<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<attendee-list>
    <div class="panel panel-default">
        <div class="panel-heading">Attendee</div>

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
                    <td><a href="{ attendee.link }" class="btn btn-default btn-sm active" role="button">Edit</a></td>
                </tr>
            </tbody>
        </table>
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
                    var summit_id = $('#summit_id').val();
                    $.getJSON('api/v1/summits/'+self.summit_id+'/attendees',{page:newPage, items:self.page_data.limit},function(data){
                        self.attendees = data;
                        self.page_data.page = newPage;
                        self.update();
                    });
                }
            }

            $('#attendees-pager').bootstrapPaginator(options);
        });

    </script>

</attendee-list>