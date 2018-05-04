<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<reports-admin-room-report>

    <div class="row" style="margin-bottom:30px;">
        <div class="col-md-3">
            <label>Filter by Event Type</label>
            <select id="event_type" style="width:100%">
                <option value="presentation">Presentations Only</option>
                <option value="all">All Events</option>
            </select>
        </div>
        <div class="col-md-3">
            <label>Filter by Room</label>
            <select id="select_venue" style="width:100%" multiple data-placeholder="Choose one or more rooms...">
                <option value="0">TBA</option>
                <option value="{ id }" title="{ getLocationOptionTitle(class_name) }" each={ locations } class="{ getLocationOptionCSSClass(class_name) }">{ name }</option>
            </select>
        </div>
        <div class="col-md-3">
            <label>Filter by Category</label>
            <select id="select_track" style="width:100%" multiple data-placeholder="Choose one or more categories...">
                <option value="{ id }" each={ tracks } >{ title }</option>
            </select>
        </div>
        <div class="col-md-3">
            <label>Sort by</label>
            <select id="sort_by" style="width:100%" placeholder="Sort By">
                <option value="start_date">Time</option>
                <option value="room">Room</option>
                <option value="code">Code</option>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
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
                    <th width="40%">Event</th>
                    <th width="10%">Room</th>
                    <th width="10%">Venue</th>
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
                    <td>{ event.venue }</td>
                    <td>{ event.capacity }</td>
                    <td>{ event.speakers }</td>
                    <td><input type="text" class="headcount" value={ event.headcount } /></td>
                    <td>
                        { event.total }
                        <i class="fa fa-download export_room_attendees" aria-hidden="true"></i>
                    </td>
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
        this.tracks          = opts.tracks;
        var self             = this;


        this.on('mount', function() {
            self.getReport();

            $('.reports-wrapper').on('change','input',function(){
                $(this).parents('tr').addClass('changed');
            });

            $('.reports-wrapper').on('change', '#event_type', function(){
                self.getReport();
            });

            $('.reports-wrapper').on('change', '#sort_by', function(){
                self.getReport();
            });

            $('.reports-wrapper').on('change', '#select_venue', function(){
                self.getReport();
            });

            $('.reports-wrapper').on('change', '#select_track', function(){
                self.getReport();
            });


            $('.reports-wrapper').on('click','.export_room_attendees',function(){
                var event_id = $(this).parents('tr').data('id');
                window.open('api/v1/summits/'+self.summit_id+'/reports/export/room_report/attendees/'+event_id, '_blank');
            });

            $('#select_venue').chosen();
            $('#select_track').chosen();
            $('#event_type').chosen({disable_search: true});
            $('#sort_by').chosen({disable_search: true});
        });

        getReport() {
            $('body').ajax_loader();
            var event_type = $('#event_type').val();
            var sort_by = $('#sort_by').val();
            var venues = ($('#select_venue').val()) ? $('#select_venue').val().join(',') : '';
            var tracks = ($('#select_track').val()) ? $('#select_track').val().join(',') : '';

            $.getJSON('api/v1/summits/'+self.summit_id+'/reports/room_report', {event_type: event_type, tracks: tracks, sort_by: sort_by, venues: venues}, function(data){
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

        self.dispatcher.on(self.dispatcher.EXPORT_ROOM_REPORT,function() {
            var event_type = $('#event_type').val();
            var sort_by = $('#sort_by').val();
            var venues = ($('#select_venue').val()) ? $('#select_venue').val().join(',') : '';
            var tracks = ($('#select_track').val()) ? $('#select_track').val().join(',') : '';

            window.open('api/v1/summits/'+self.summit_id+'/reports/export/room_report?event_type='+event_type+'&sort_by='+sort_by+'&venues='+venues+'&tracks='+tracks, '_blank');
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