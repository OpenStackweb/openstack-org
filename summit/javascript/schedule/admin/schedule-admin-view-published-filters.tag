<schedule-admin-view-published-filters>
        <div class="row">
            <div class="col-md-12" style="margin:10px 0;">
                <div class="input-group" style="width: 100%;">
                    <input data-rule-required="true" data-rule-minlength="3" type="text" id="published_search_term" class="form-control input-global-search" placeholder="Search for published Events">
                    <span class="input-group-btn" style="width: 5%;">
                        <button class="btn btn-default btn-global-search" id="search_published" ><i class="fa fa-search"></i></button>
                        <button class="btn btn-default btn-global-search-clear" onclick={ clearClicked }>
                            <i class="fa fa-times"></i>
                        </button>
                    </span>
                </div>
            </div>
        </div>
        <div class="row" style="margin-bottom: 35px;">
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
                    <option value="{ id }" each={ id, location in summit.locations } >{ location.name }</option>
                    <option value="0">TBA</option>
                </select>
            </div>
        </div>

        <script>
                this.summit     = opts.summit;
                this.day        = '';
                this.location_id = '';
                this.dispatcher = opts.dispatcher;
                var self        = this;

                this.on('updated', function(){
                    $('#select_day').val(self.day);
                    $('#select_venue').val(self.location_id);
                });


                this.on('mount', function(){
                    $(function() {
                        $('#select_day').change(function(e){
                            self.day         = $('#select_day').val();
                            self.location_id = $('#select_venue').val();
                            self.clearClicked();
                            self.doFilter();
                        });

                        $('#select_venue').change(function(e) {
                            self.day         = $('#select_day').val();
                            self.location_id = $('#select_venue').val();
                            self.clearClicked();
                            self.doFilter();
                        });

                        $('#search_published').click(function(e) {
                            var search_term = $('#published_search_term').val();

                            if (search_term) {
                                self.doSearch(search_term);
                            } else {

                            }
                        });

                        $("#published_search_term").keydown(function (e) {
                            if (e.keyCode == 13) {
                                $('#search_published').click();
                            }
                        });

                        self.lockHash();

                        self.doFilter();
                    });
                });

                doFilter() {
                    if(self.day === '' || self.location_id === '') return;
                    $('body').ajax_loader();

                    self.dispatcher.publishedEventsFilterChanged(self.summit.id, self.day ,self.location_id);
                }

                doSearch(term) {
                    $('body').ajax_loader();
                    $('#schedule_container').hide();
                    $('#search_results').show();
                    self.dispatcher.publishedEventsSearch(self.summit.id, term);
                }

                clearClicked(){
                    $('#published_search_term').val('');
                    window.location.hash = '';
                    $('#schedule_container').show();
                    $('#search_results').hide();
                }

                lockHash() {
                    // read url hash and redirect to event
                    var hash = $(window).url_fragment('getParams');

                    if(!$.isEmptyObject(hash)){
                        for(var key in hash) {
                            var value = hash[key];

                            switch(key) {
                                case 'day':
                                    $('#select_day').val(value);
                                    self.day = value;
                                    break;
                                case 'venue':
                                    $('#select_venue').val(value);
                                    self.location_id = value;
                                    break;
                            }
                        }
                    }
                }

                self.dispatcher.on(self.dispatcher.PUBLISHED_EVENTS_DEEP_LINK, function()
                {
                    $('#published_search_term').val('');
                    $('#schedule_container').show();
                    $('#search_results').hide();

                    $('body').ajax_loader();

                    self.lockHash();
                    self.doFilter();

                });


        </script>
</schedule-admin-view-published-filters>