<schedule-admin-view-published-filters>
        <div class="row published-filters">
            <div class="col-md-12" style="margin:10px 0;">
                <div class="input-group" style="width: 100%;">
                    <input data-rule-required="true" data-rule-minlength="3" type="text" id="published_search_term" class="form-control input-global-search" placeholder="Search for published Events">
                    <span class="input-group-btn" style="width: 5%;">
                        <button class="btn btn-default btn-global-search" id="search_published" ><i class="fa fa-search"></i></button>
                        <button class="btn btn-default btn-global-search-clear" onclick={ resetFilters }>
                            <i class="fa fa-times"></i>
                        </button>
                        <button class="btn btn-default" data-toggle="modal" data-target="#empty_spots_modal">Find Empty</button>
                    </span>
                </div>
            </div>
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
                    <option hidden value=''>-- Select A Venue --</option>
                    <option value="0">TBA</option>
                    <option value="{ id }" title="{ getLocationOptionTitle(class_name) }" each={ summit.locations } data-venue-name="{ name }" class="{ getLocationOptionCSSClass(class_name) }">{ name }</option>
                </select>
            </div>
        </div>
        <!-- Empty Spots Modal -->
        <div id="empty_spots_modal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Find Empty Spots</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row" style="margin-bottom: 35px;">
                            <div class="col-md-6">
                                <label for="select_day_modal">Day</label>
                                <select id="select_day_modal" style="width: 80%">
                                    <option value=''>-- Any Day --</option>
                                    <option value={ day.date } each={  key, day in summit.dates }>{ day.label }</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="select_venue_modal">Venue</label>
                                <select id="select_venue_modal" style="width: 80%">
                                    <option value=''>-- Any Venue --</option>
                                    <option value="{ id }" title="{ getLocationOptionTitle(class_name) }" each={ summit.locations } class="{ getLocationOptionCSSClass(class_name) }">{ name }</option>
                                </select>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 35px;">
                            <div class="col-md-3">
                                <label for="start_time_modal">From </label>
                                <input type='text' id="start_time_modal" value="07:00" style="width: 60%"/>
                            </div>
                            <div class="col-md-3">
                                <label for="end_time_modal" >To </label>
                                <input type='text' id="end_time_modal" value="22:00" style="width: 60%"/>
                            </div>
                            <div class="col-md-6">
                                <label for="end_time_modal">Gap (minutes)</label>
                                <input type='number' id="length_modal" min="15" max="240" step="15" value="60" style="width: 60px"/>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="search_empty" class="btn btn-default" data-dismiss="modal">Find</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
                this.summit      = opts.summit;
                this.day         = '';
                this.location_id = '';
                this.time        = '';
                this.event_id    = '';
                this.query       = '';
                this.dispatcher  = opts.dispatcher;
                this.api         = opts.api;
                var self         = this;

                this.on('updated', function(){
                    self.updateFilters();
                });

                window.onhashchange = function(event)
                {
                    self.setFromHash();
                    self.doFilter();
                };

                this.on('mount', function(){
                    $(function() {
                        $('#select_day').change(function(e){
                            self.resetFilters();
                            self.buildHash();
                            self.dispatcher.publishedEventsDayChanged(self.summit.id, self.day);
                            //self.doFilter();
                        });

                        $('#select_venue').change(function(e) {
                            self.resetFilters();
                            self.buildHash();
                            //self.doFilter();
                        });

                        $('#select_day').chosen();
                        $('#select_venue').chosen();

                        $('#search_published').click(function(e) {
                            var search_term = $('#published_search_term').val();
                            if (search_term) {
                                self.query = search_term;
                                self.buildHash();
                                //self.doFilter();
                            }
                        });

                        $("#published_search_term").keydown(function (e) {
                            if (e.keyCode == 13) {
                                $('#search_published').click();
                            }
                        });

                        $('#search_empty').click(function(e) {
                            self.doSearchEmpty();
                        });

                        $('#start_time_modal,#end_time_modal').datetimepicker({
                            datepicker:false,
                            format:'H:i',
                            minTime:'07:00',
                            maxTime:'22:00',
                            step: 15
                        });

                        self.setFromHash();
                        self.doFilter();
                    });
                });

                doFilter() {
                    if (self.query) {
                        self.doSearch();
                    } else {
                        if(self.day === '' || self.location_id === '') return;
                        $('body').ajax_loader();
                        self.dispatcher.publishedEventsFilterChanged(self.summit.id, self.day ,self.location_id);
                    }
                }

                doSearch() {
                    $('body').ajax_loader();
                    $('#schedule_container').hide();
                    $('#empty_spots').hide();
                    $('#search_results').show();
                    self.dispatcher.publishedEventsSearch(self.summit.id, self.query);
                }

                clearClicked(){
                    $('#published_search_term').val('');
                    $('#schedule_container').show();
                    $('#search_results').hide();
                    $('#empty_spots').hide();
                }

                resetFilters() {
                    self.query = '';
                    self.event_id = '';
                    self.day = $('#select_day').val();
                    self.location_id = $('#select_venue').val();
                    self.time = '';

                    $('#published_search_term').val('');
                    $('#schedule_container').show();
                    $('#search_results').hide();
                }

                doSearchEmpty() {
                    $('body').ajax_loader();
                    $('#schedule_container').hide();
                    $('#search_results').hide();
                    $('#empty_spots').show();
                    window.location.hash = '';

                    var days = [];
                    var venues = [];
                    var start_time = $('#start_time_modal').val();
                    var end_time = $('#end_time_modal').val();
                    var length = $('#length_modal').val() * 60;

                    // add all options if Any selected
                    if ($('#select_day_modal').val() == '') {
                        $("#select_day_modal option").each(function(){
                            if ($(this).val() != '') days.push($(this).val());
                        });
                    } else {
                        days.push($('#select_day_modal').val());
                    }

                    if ($('#select_venue_modal').val() == '') {
                        $("#select_venue_modal option").each(function(){
                            if ($(this).val() != '') venues.push($(this).val());
                        });
                    } else {
                        venues.push($('#select_venue_modal').val());
                    }

                    self.dispatcher.publishedEventsSearchEmpty(self.summit.id,days,start_time,end_time,venues,length);
                }

                setFromHash() {
                    // read url hash and redirect to event
                    var hash = $(window).url_fragment('getParams');
                    self.day = '';
                    self.location_id = '';
                    self.event_id = '';
                    self.time = '';
                    self.query = '';
                    if(!$.isEmptyObject(hash)){
                        for(var key in hash) {
                            var value = hash[key];

                            switch(key) {
                                case 'day':
                                    self.day = value;
                                    self.dispatcher.publishedEventsDayChanged(self.summit.id, self.day);
                                    break;
                                case 'venue':
                                    self.location_id = value;
                                    break;
                                case 'event':
                                    self.event_id = value;
                                    break;
                                case 'time':
                                    self.time = value;
                                    break;
                                case 'q':
                                    self.query = value;
                                    break;
                            }
                        }
                    }
                    self.updateFilters();
                }

                buildHash() {
                    $(window).url_fragment('setParam','q', '');
                    $(window).url_fragment('setParam','day', '');
                    $(window).url_fragment('setParam','venue', '');
                    $(window).url_fragment('setParam','event', '');
                    $(window).url_fragment('setParam','time', '');

                    if (self.query) {
                        $(window).url_fragment('setParam','q', self.query);
                    } else {
                        $(window).url_fragment('setParam','day', self.day);
                        $(window).url_fragment('setParam','venue', self.location_id);
                        $(window).url_fragment('setParam','event', self.event_id);
                        $(window).url_fragment('setParam','time', self.time);
                    }

                    window.location.hash =  $(window).url_fragment('serialize');
                }

                updateFilters() {
                    $('#select_day').val(self.day);
                    $("#select_day").trigger("chosen:updated");
                    $('#select_venue').val(self.location_id);
                    $("#select_venue").trigger("chosen:updated");
                    $('#published_search_term').val(self.query);
                }

                self.dispatcher.on(self.dispatcher.PUBLISHED_EVENTS_DEEP_LINK, function()
                {
                    $('#published_search_term').val('');
                    $('#schedule_container').show();
                    $('#search_results').hide();
                    $('#empty_spots').hide();

                    $('body').ajax_loader();

                    self.setFromHash();
                    //self.doFilter();
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

                self.api.on(self.api.RETRIEVED_LOCATIONS_BY_DAY, function(data){

                    $.each(data.locations, function(idx, location){
                        var option = $('#select_venue option[value="'+location.id+'"]');
                        option.text(option.attr('data-venue-name')+' ('+location.events_count+')');
                    });
                    $('#select_venue').trigger("chosen:updated");
                });

        </script>
</schedule-admin-view-published-filters>