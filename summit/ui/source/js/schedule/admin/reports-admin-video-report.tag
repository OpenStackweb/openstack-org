<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<reports-admin-video-report>
    <div class="row" style="margin-bottom:30px;">
        <div class="col-md-12">
            <label for="tracks">Categories</label>
            <select id="tracks" style="width:100%" multiple>
                <option value="{ track.id }" each={ track in tracks } selected={ this.parent.isTrackSelected(track.id) } >{ track.title }</option>
            </select>
        </div>
    </div>
    <div class="row" style="margin-bottom:30px;">
        <div class="col-md-4 venues_col">
            <label>Venues</label><br>
            <select id="venues" style="width:100%" multiple data-placeholder="Choose one or more rooms...">
                <option value="0">TBA</option>
                <option value="{ id }" title="{ getLocationOptionTitle(class_name) }" each={ locations } class="{ getLocationOptionCSSClass(class_name) }">{ name }</option>
            </select>
        </div>
        <div class="col-md-4">
            <label>Event Date</label><br>
            From <input class="form-control" id="start_date" />
            To <input class="form-control" id="end_date" />
        </div>
        <div class="col-md-4">
            <label>Search</label><br>
            <div class="input-group" style="width: 100%;">
                <input data-rule-required="true" data-rule-minlength="3" type="text" id="search_term" class="form-control input-global-search" placeholder="Search Title, Tag or Description">
                <span class="input-group-btn" style="width: 5%;">
                <button class="btn btn-default btn-global-search" onclick={ searchReport }><i class="fa fa-search"></i></button>
                <button class="btn btn-default btn-global-search-clear" onclick={ clearSearch }>
                    <i class="fa fa-times"></i>
                </button>
                </span>
            </div>
        </div>
    </div>
    <div class="panel panel-default" each="{ key, day in report_data }" if={ day.length > 0 }>
        <div class="panel-heading">{ key }</div>

        <table class="table">
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="10%">Time</th>
                    <th>Tags</th>
                    <th>Event</th>
                    <th width="30%">Description</th>
                    <th width="10%">Room</th>
                    <th width="10%">Venue</th>
                    <th>Display</th>
                    <th>YoutubeID</th>
                </tr>
            </thead>
            <tbody>
                <tr each={ event in day } data-id="{ event.id }">
                    <td>{ event.id }</td>
                    <td>{ event.start_time } - { event.end_time }</td>
                    <td>{ event.tags }</td>
                    <td>{ event.title }</td>
                    <td><raw content={ event.description }></raw></td>
                    <td>{ event.room }</td>
                    <td>{ event.venue }</td>
                    <td><input type="checkbox" class="display_video" checked={ event.display } if={ event.youtube }/></td>
                    <td>{ event.youtube }</td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        this.dispatcher      = opts.dispatcher;
        this.summit_id       = opts.summit_id;
        this.report_data     = [];
        this.tracks          = opts.tracks;
        this.report_tracks   = [];
        this.locations       = opts.locations;
        var self             = this;


        this.on('mount', function() {
            self.getReport();
            $('#tracks').chosen();
            $('#venues').chosen();

            $('#start_date').datepicker();
            $('#end_date').datepicker();

            $('.reports-wrapper').on('change','input',function(){
                $(this).parents('tr').addClass('changed');
            });

            $('#tracks').change(function(){
                $(this).addClass('tracks_changed');
                self.getReport();
            });

            $('#venues').change(function(){
                self.getReport();
            });

            $('#start_date').change(function(){
                self.getReport();
            });

            $('#end_date').change(function(){
                self.getReport();
            });

            $("#search_term").keydown(function (e) {
                if (e.keyCode == 13) {
                    self.searchReport();
                }
            });


        });

        getReport() {
            $('body').ajax_loader();
            var request = {
                tracks: ($('#tracks').val()) ? $('#tracks').val().join(',') : '',
                venues: ($('#venues').val()) ? $('#venues').val().join(',') : '',
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                search_term: $('#search_term').val()
            };

            $.getJSON('api/v1/summits/'+self.summit_id+'/reports/video_report', request, function(data){
                self.report_data = data.report;
                self.report_tracks = data.tracks;
                self.update();
                $('#tracks').trigger("chosen:updated");
                $('body').ajax_loader('stop');
            });
        }

        isTrackSelected(track_id) {
            return (!self.report_tracks || $.inArray(track_id.toString(),self.report_tracks) != -1);
        }

        self.dispatcher.on(self.dispatcher.SAVE_VIDEO_REPORT,function(report) {
            var report_data = [];
            $('.changed').each(function(){
                var id = $(this).data('id');
                var display_video = $('.display_video',this).prop('checked');
                report_data.push({id: id, display_video: display_video});
            });
            var selected_tracks = ($('#tracks').val()) ? $('#tracks').val().join(',') : 'all';
            var request = {report_config:{config_name:'Tracks',config_value:selected_tracks}, report_data: report_data}

            if (report_data.length || $('.tracks_changed').length) {
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

        self.dispatcher.on(self.dispatcher.EXPORT_VIDEO_REPORT,function() {
            var tracks = ($('#tracks').val()) ? $('#tracks').val().join(',') : '';
            var venues = ($('#venues').val()) ? $('#venues').val().join(',') : '';
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var search_term = $('#search_term').val();

            window.open('api/v1/summits/'+self.summit_id+'/reports/export/video_report?tracks='+tracks+'&venues='+venues+'&start_date='+start_date+'&end_date='+end_date+'&term='+search_term, '_blank');
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

        searchReport() {
            self.getReport();
        }

        clearSearch() {
            $('#search_term').val('');
            self.getReport();
        }

    </script>

</reports-admin-video-report>