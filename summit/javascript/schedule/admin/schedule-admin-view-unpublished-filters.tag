<schedule-admin-view-unpublished-filters>
        <div class="row">
            <div class="col-md-6">
                    <label for="select_unpublished_events_source">Source</label>
                    <select id="select_unpublished_events_source" name="select_unpublished_events_source">
                        <option value=''>-- Select An Event Source --</option>
                        <option value='tracks'>Tracks List</option>
                        <option value='presentations'>Presentations</option>
                        <option value='events'>Summit Events</option>
                    </select>
            </div>
            <div class="col-md-6" id="track_list_col" style="display:none;">
                    <label for="select_track_list">Track Lists</label>
                    <select id="select_track_list" name="select_track_list">
                        <option value=''>-- All --</option>
                    </select>
            </div>
        </div>
        <script>
            this.summit = opts.summit;
            this.api    = opts.api;
            var self    = this;

            this.on('mount', function(){
                $(function() {
                    $('#select_unpublished_events_source').change(function(e){
                        var source = $('#select_unpublished_events_source').val();
                        if(source === '') return;
                        if(source === 'tracks') $('#track_list_col').show();
                        else $('#track_list_col').hide();
                        self.doFilter(source, '');
                    });

                    $('#select_track_list').change(function(e){
                        var source       = $('#select_unpublished_events_source').val();
                        var track_list_id = $('#select_track_list').val();
                        self.doFilter(source, track_list_id);
                    });
                });
            });

            doFilter(source, track_list_id) {
                self.api.getUnpublishedEventsBySource(self.summit.id, source ,track_list_id);
            }
        </script>
</schedule-admin-view-unpublished-filters>