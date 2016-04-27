<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<reports-admin-video-report>
    <div class="row" style="margin-bottom:30px;">
        <div class="col-md-12">
            <select id="tracks" style="width:100%" multiple>
                <option value="{ track.id }" each={ track in tracks } selected={ this.parent.isTrackSelected(track.id) } >{ track.title }</option>
            </select>
        </div>
    </div>
    <div class="panel panel-default" each="{ key, day in report_data }">
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
        var self             = this;


        this.on('mount', function() {
            self.getReport();
            $('#tracks').chosen();

            $('.reports-wrapper').on('change','input',function(){
                $(this).parents('tr').addClass('changed');
            });

            $('#tracks').change(function(){
                $(this).addClass('tracks_changed');
                self.getReport();
            });

        });

        getReport() {
            $('body').ajax_loader();
            var selected_tracks = ($('#tracks').val()) ? $('#tracks').val().join(',') : '';

            $.getJSON('api/v1/summits/'+self.summit_id+'/reports/video_report', {tracks: selected_tracks}, function(data){
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
            var tracks = ($('#tracks').val()) ? $('#tracks').val().join(',') : 'all';
            window.open('api/v1/summits/'+self.summit_id+'/reports/export/video_report?tracks='+tracks, '_blank');
        });

    </script>

</reports-admin-video-report>