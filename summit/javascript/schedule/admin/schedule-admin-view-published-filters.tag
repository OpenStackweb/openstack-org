<schedule-admin-view-published-filters>
        <div class="row">
            <div class="col-md-6">
                <label for="select_day">Day</label>
                <select id="select_day" name="select_day" style="width: 80%">
                 <option value=''>-- Select A Day --</option>
                    <option value={ day.date } each={  key, day in summit.dates }>{ day.label }</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="select_venue">Venue</label>
                <select id="select_venue" name="select_venue" style="width: 80%">
                    <option value=''>-- Select A Venue --</option>
                    <option value="0">TBA</option>
                    <option value="{ id }" each={ id, location in summit.locations } >{ location.name }</option>
                </select>
            </div>
        </div>

        <script>
                this.summit     = opts.summit;
                this.dispatcher = opts.dispatcher;
                var self        = this;

                this.on('mount', function(){
                    $(function() {
                        $('#select_day').change(function(e){
                            var day         = $('#select_day').val();
                            var location_id = $('#select_venue').val();
                            self.doFilter(day, location_id);
                        });

                        $('#select_venue').change(function(e) {
                            var day         = $('#select_day').val();
                            var location_id = $('#select_venue').val();
                            self.doFilter(day, location_id);
                        });

                        var day         = $('#select_day').val();
                        var location_id = $('#select_venue').val();
                        self.doFilter(day, location_id);
                    });
                });

                doFilter(day, location_id) {
                    if(day ==='' || location_id === '') return;
                    $('body').ajax_loader();
                    self.dispatcher.publishedEventsFilterChanged(self.summit.id, day ,location_id);
                }
        </script>
</schedule-admin-view-published-filters>