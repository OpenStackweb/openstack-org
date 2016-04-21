<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<reports-admin-room-report>

    <div class="row" style="margin-bottom:30px;">
        <div class="col-md-3">
            <select id="event_type" style="width:100%">
                <option value="presentation">Presentations Only</option>
                <option value="all">All Events</option>
            </select>
        </div>
        <div class="col-md-3">
            <select id="select_venue" style="width:100%">
                <option value='all'>All Venues</option>
                <option value="0">TBA</option>
                <option value="{ id }" title="{ getLocationOptionTitle(class_name) }" each={ locations } class="{ getLocationOptionCSSClass(class_name) }">{ name }</option>
            </select>
        </div>
        <div class="col-md-6 pull-right">
            Users with calendar: { calendar_count }
        </div>
    </div>
    <div class="panel panel-default" each="{ key, day in report_data }">
        <div class="panel-heading">{ key }</div>

        <table class="table">
            <thead>
                <tr>
                    <th width="10%">Time</th>
                    <th>Code</th>
                    <th width="50%">Event</th>
                    <th width="10%">Room</th>
                    <th>Capacity</th>
                    <th>Speakers</th>
                    <th width="10%">HeadCount</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr each={ event in day } data-id="{ event.id }">
                    <td>{ event.start_time } - { event.end_time }</td>
                    <td>{ event.code }</td>
                    <td>{ event.title }</td>
                    <td>{ event.room }</td>
                    <td>{ event.capacity }</td>
                    <td>{ event.speakers }</td>
                    <td><input type="text" class="headcount" value={ event.headcount } /></td>
                    <td>{ event.total }</td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        this.dispatcher      = opts.dispatcher;
        this.summit_id       = opts.summit_id;
        this.report_data     = [];
        this.calendar_count  = 0;
        this.locations       = opts.locations;
        var self             = this;


        this.on('mount', function() {
            self.getReport();

            $('.reports-wrapper').on('change','input',function(){
                $(this).parents('tr').addClass('changed');
            });

            $('#event_type').change(function(){
                self.getReport();
            });

            $('#select_venue').change(function(){
                self.getReport();
            });

            $('#select_venue').chosen();
            $('#event_type').chosen({disable_search: true});
        });

        getReport() {
            $('body').ajax_loader();
            var event_type = $('#event_type').val();
            var venue = $('#select_venue').val();

            $.getJSON('api/v1/summits/'+self.summit_id+'/reports/room_report', {event_type: event_type, venue: venue}, function(data){
                self.report_data = data.report;
                self.calendar_count = data.calendar_count;
                self.update();
                $('body').ajax_loader('stop');
            });
        }

        self.dispatcher.on(self.dispatcher.SAVE_ROOM_REPORT,function(report) {
            var request = [];
            $('.changed').each(function(){
                var id = $(this).data('id');
                var headcount = $('.headcount',this).val();
                request.push({id: id, headcount: headcount});
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
                        swal('Error', 'There was a problem saving the event, please contact admin.', 'warning');
                    }
                });
            }
        });

        getLocationOptionCSSClass(class_name) {
        switch(class_name) {
        case 'SummitVenue':
        return 'location-venue';
        break;
        case 'SummitHotel':
        return 'location-hotel';
        break;
        case 'SummitExternalLocation':
        return 'location-external';
        break;
        case 'SummitVenueRoom':
        return 'location-venue-room';
        break;
        }
        }

        getLocationOptionTitle(class_name) {
        switch(class_name) {
        case 'SummitVenue':
        return 'Venue';
        break;
        case 'SummitHotel':
        return 'Hotel';
        break;
        case 'SummitExternalLocation':
        return 'External Location';
        break;
        case 'SummitVenueRoom':
        return 'Room';
        break;
        }
        }

    </script>

</reports-admin-room-report>