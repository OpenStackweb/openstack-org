<schedule-admin-view-unpublished-filters>
    <div class="row filters">
        <div class="col-md-12" style="margin:10px 0;">
            <div class="input-group" style="width: 100%;">
                <input data-rule-required="true" data-rule-minlength="3" type="text" id="unpublished_search_term" class="form-control input-global-search" placeholder="Search for unpublished Events">
                <span class="input-group-btn" style="width: 5%;">
                    <button class="btn btn-default btn-global-search" id="search_unpublished"><i class="fa fa-search"></i></button>
                    <button class="btn btn-default btn-global-search-clear" onclick={ clearClicked }>
                        <i class="fa fa-times"></i>
                    </button>
                </span>
            </div>
        </div>
        <div class="col-md-10">
            <div class="row">
                <div class="col-md-5">
                    <label for="select_unpublished_events_source">Source</label>
                    <select id="select_unpublished_events_source" name="select_unpublished_events_source" style="width: 70%" value="presentations">
                        <option value=''>-- Select An Event Source --</option>
                        <option value='tracks'>Tracks</option>
                        <option value='track_list'>Track Chair Sel.</option>
                        <option value='presentations' selected>Presentations</option>
                        <option value='events'>Summit Events</option>
                    </select>
                </div>
                <div class="col-md-7">
                    <div id="track_list_col" style="display:none;">
                        <label for="select_track_list">Track Lists</label>
                        <select id="select_track_list" name="select_track_list" style="width: 70%">
                            <option value=''>-- All --</option>
                            <option each="{ list in summit.track_lists }" value='{ list.id }'>{ list.name }</option>
                        </select>
                    </div>
                    <div id="track_col" style="display:none;">
                        <label for="select_tracks">Tracks</label>
                        <select id="select_tracks" name="select_tracks" style="width: 70%">
                            <option value=''>-- All --</option>
                            <option each="{ list in summit.tracks }" value='{ list.id }'>{ list.name }</option>
                        </select>
                    </div>
                    <div id="event_type_col" style="display:none;">
                        <label for="select_event_type">Type</label>
                        <select id="select_event_type" name="select_event_type" style="width: 70%">
                            <option value=''>-- All --</option>
                            <option each="{ id, type in summit.event_types }" if={ type.type != 'Presentation' } value='{ id }'>{ type.type }</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <label for="sort_list">Sort by</label>
                    <select id="sort_list" name="sort_list" style="width: 70%" value="title">
                        <option value='SummitEvent.Title' selected>Title</option>
                        <option value='SummitEvent.ID'>Event Id</option>
                        <option value='SummitEvent.StartDate'>Start Date</option>
                    </select>
                </div>
                <div class="col-md-7">
                    <div id="event_status_col">
                        <label for="select_unpublished_events_status">Status</label>
                        <select id="select_unpublished_events_status" name="select_unpublished_events_status" style="width: 70%" value="approved">
                            <option value=''>All</option>
                            <option each="{ status in summit.status_options }" value='{ status }'>{ status }</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary unpublished-events-refresh" title="refresh unpublished events">
                Refresh
            </button>
        </div>
    </div>
    <div class="row filters">
        <div class="col-md-5">

        </div>
        <div class="col-md-7">

        </div>
    </div>
    <div class="row filters">
        <div class="col-md-5">

        </div>
        <div class="col-md-7">

        </div>
    </div>

    <script>
        this.summit     = opts.summit;
        this.api        = opts.api;
        this.dispatcher = opts.dispatcher;
        var self        = this;

        this.on('mount', function(){
            $(function() {
                $('#select_unpublished_events_source').change(function(e){
                    var source = $('#select_unpublished_events_source').val();
                    var second_source = '';
                    var search_term = $('#unpublished_search_term').val();
                    var status = $('#select_unpublished_events_status').val();
                    var order = $('#sort_list').val();

                    self.dispatcher.unpublishedEventsSourceChanged(source);
                    if(source === '') {
                        $('#track_list_col').hide();
                        $('#event_status_col').hide();
                        return;
                    }

                    switch (source) {
                        case 'tracks':
                            $('#track_col').show();
                            $('#track_list_col').hide();
                            $('#event_type_col').hide();
                            $('#event_status_col').show();
                            second_source = $('#select_tracks').val();
                            break;
                        case 'track_list':
                            $('#track_list_col').show();
                            $('#track_col').hide();
                            $('#event_type_col').hide();
                            $('#event_status_col').show();
                            $('#sort_list').append('<option value="SummitSelectedPresentation.Order">Track Chair List Order</option>');
                            second_source = $('#select_track_list').val();
                            break;
                        case 'events':
                            $('#track_list_col').hide();
                            $('#track_col').hide();
                            $('#event_type_col').show();
                            $('#event_status_col').hide();
                            $("#sort_list option[value='SummitSelectedPresentation.Order']").remove();
                            second_source = $('#select_event_type').val();
                            break;
                        default:
                            $('#track_list_col').hide();
                            $('#track_col').hide();
                            $('#event_type_col').hide();
                            $('#event_status_col').show();
                            $("#sort_list option[value='SummitSelectedPresentation.Order']").remove();
                            break;
                    }

                    self.doFilter(source,second_source,status,search_term,order);
                });

                $('#select_track_list').change(function(e){
                    var source        = $('#select_unpublished_events_source').val();
                    var track_list_id = $('#select_track_list').val();
                    var status        = $('#select_unpublished_events_status').val();
                    var search_term   = $('#unpublished_search_term').val();
                    var order         = $('#sort_list').val();

                    self.doFilter(source,track_list_id,status,search_term,order);
                });

                $('#select_tracks').change(function(e){
                    var source        = $('#select_unpublished_events_source').val();
                    var track_id      = $('#select_tracks').val();
                    var status        = $('#select_unpublished_events_status').val();
                    var search_term   = $('#unpublished_search_term').val();
                    var order         = $('#sort_list').val();

                    self.doFilter(source,track_id,status,search_term,order);
                });

                $('#select_event_type').change(function(e){
                    var source        = $('#select_unpublished_events_source').val();
                    var event_type_id = $('#select_event_type').val();
                    var status        = $('#select_unpublished_events_status').val();
                    var search_term   = $('#unpublished_search_term').val();
                    var order         = $('#sort_list').val();

                    self.doFilter(source,event_type_id,status,search_term,order);
                });

                $('#select_unpublished_events_status').change(function(e){
                    var source        = $('#select_unpublished_events_source').val();
                    var status        = $('#select_unpublished_events_status').val();
                    var search_term   = $('#unpublished_search_term').val();
                    var order         = $('#sort_list').val();

                    if (source) {
                        var second_source = self.secondarySource(source);
                        self.doFilter(source,second_source,status,search_term,order);
                    }
                });

                $('.unpublished-events-refresh').click(function(e){
                    var source       = $('#select_unpublished_events_source').val();
                    var status        = $('#select_unpublished_events_status').val();
                    var search_term = $('#unpublished_search_term').val();
                    var order = $('#sort_list').val();

                    if (source) {
                        var second_source = self.secondarySource(source);
                        self.doFilter(source,second_source,status,search_term,order);
                    }
                });

                $('#sort_list').change(function(e) {
                    var source       = $('#select_unpublished_events_source').val();
                    var status        = $('#select_unpublished_events_status').val();
                    var search_term = $('#unpublished_search_term').val();
                    var order = $('#sort_list').val();

                    if (source) {
                        var second_source = self.secondarySource(source);
                        self.doFilter(source,second_source,status,search_term,order);
                    }
                });

                $('#search_unpublished').click(function(e) {
                    var source       = $('#select_unpublished_events_source').val();
                    var status        = $('#select_unpublished_events_status').val();
                    var search_term = $('#unpublished_search_term').val();
                    var order = $('#sort_list').val();

                    if (source) {
                        var second_source = self.secondarySource(source);
                        self.doFilter(source,second_source,status,search_term,order);
                    } else {
                        swal("Select a Source", "Please select a source to search on.", "warning");
                    }
                });

                $("#unpublished_search_term").keydown(function (e) {
                    if (e.keyCode == 13) {
                        $('#search_unpublished').click();
                    }
                });

                self.doFilter('presentations', '','','','SummitEvent.Title');
            });
        });

        doFilter(source, second_source, status, search_term, order)
        {
            $('body').ajax_loader();
            var page_size = $('#page-size').val();
            self.api.getUnpublishedEventsBySource(self.summit.id, source ,second_source, status, search_term, order, 1, page_size);
        }

        self.dispatcher.on(self.dispatcher.UNPUBLISHED_EVENTS_PAGE_CHANGED, function(page_nbr)
        {
            var source        = $('#select_unpublished_events_source').val();
            var status        = $('#select_unpublished_events_status').val();
            var track_list_id = $('#select_track_list').val();
            var search_term   = $('#unpublished_search_term').val();
            var order         = $('#sort_list').val();
            var page_size     = $('#page-size').val();

            self.api.getUnpublishedEventsBySource(self.summit.id, source ,track_list_id, status, search_term, order, page_nbr, page_size);
        });

        clearClicked(e){

            $('#unpublished_search_term').val('');

            var source       = $('#select_unpublished_events_source').val();
            var status        = $('#select_unpublished_events_status').val();
            var track_list_id = $('#select_track_list').val();
            var search_term = $('#unpublished_search_term').val();
            var order = $('#sort_list').val();

            if (source) {
                self.doFilter(source, track_list_id,status,search_term,order);
            }
        }

        secondarySource(source) {
            var second_source = '';

            if (source == 'events') {
                second_source = $('#select_event_type').val();
            } else if (source == 'tracks') {
                second_source = $('#select_tracks').val();
            } else if (source == 'track_list') {
                second_source = $('#select_track_list').val();
            }
            return second_source;
        }

    </script>
</schedule-admin-view-unpublished-filters>