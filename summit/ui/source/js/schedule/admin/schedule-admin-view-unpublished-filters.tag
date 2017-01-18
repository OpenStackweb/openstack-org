<schedule-admin-view-unpublished-filters>
    <div class="row unpublished-filters">
        <div class="col-md-12" style="margin:10px 0;">
            <div class="input-group" style="width: 100%;">
                <input data-rule-required="true" data-rule-minlength="3" type="text" id="unpublished_search_term" class="form-control input-global-search" placeholder="Search for unpublished Events">
                <span class="input-group-btn" style="width: 5%;">
                    <button class="btn btn-default btn-global-search unpublished_filter_button" id="search_unpublished"><i class="fa fa-search"></i></button>
                    <button class="btn btn-default btn-global-search-clear" onclick={ clearClicked }>
                        <i class="fa fa-times"></i>
                    </button>
                    <button class="btn btn-primary unpublished-events-refresh unpublished_filter_button" title="refresh unpublished events">
                        Refresh
                    </button>
                </span>
            </div>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                    <label for="unpublished_event_type">Source</label>
                    <select id="unpublished_event_type">
                        <option value=''>-- Select An Event Type --</option>
                        <option value='presentations' selected>Presentations + Panels</option>
                        <option value='lightning'>Lightning Talks</option>
                        <option value='evening_events'>Evening Events</option>
                        <option value='lunch_events'>Lunches / Breaks</option>
                        <option value='all_events'>All Events</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="unpublished_sort">Sort by</label>
                    <select id="unpublished_sort" class="unpublished_filter">
                        <option value='SummitEvent.Title' selected>Title</option>
                        <option value='SummitEvent.ID'>Event Id</option>
                        <option value='SummitEvent.StartDate'>Start Date</option>
                    </select>
                </div>
                <div id="unpublished_track_col" class="col-md-6">
                    <label for="unpublished_track">Tracks</label>
                    <select id="unpublished_track" class="unpublished_filter">
                        <option value=''>-- All --</option>
                        <option each="{ list in summit.tracks }" value='{ list.id }'>{ list.name }</option>
                    </select>
                </div>
                <div id="unpublished_selection_status_col" class="col-md-6">
                    <label for="unpublished_selection_status">Sel. Status</label>
                    <select id="unpublished_selection_status" class="unpublished_filter" value="approved">
                        <option value=''>All</option>
                        <option value='selected'>All Selected</option>
                        <option each="{ status in summit.selection_status_options }" value='{ status }'>{ status }</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <script>
        this.summit     = opts.summit;
        this.api        = opts.api;
        this.dispatcher = opts.dispatcher;
        var self        = this;

        this.on('mount', function(){
            $('#unpublished_event_type').change(function(e){
                var source           = $('#unpublished_event_type').val();
                var track_id         = $('#unpublished_track').val();
                var search_term      = $('#unpublished_search_term').val();
                var status           = $('#unpublished_selection_status').val();

                self.dispatcher.unpublishedEventsSourceChanged(source);

                if(source === '') return;

                switch (source) {
                    case 'presentations':
                    case 'lightning':
                    case 'all_events':
                        $('#unpublished_track_col').show();
                        $('#unpublished_selection_status_col').show();
                        break;
                    default:
                        $('#unpublished_track_col').hide();
                        $('#unpublished_selection_status_col').hide();
                        break;
                }
                var order = $('#unpublished_sort').val();

                self.doFilter(source,track_id,status,search_term,order);
            });

            $('.unpublished_filter').change(function(e){
                var source        = $('#unpublished_event_type').val();
                var track_id      = $('#unpublished_track').val();
                var status        = $('#unpublished_selection_status').val();
                var search_term   = $('#unpublished_search_term').val();
                var order         = $('#unpublished_sort').val();

                if (!source) swal("Select a Source", "Please select a source to search on.", "warning");

                self.doFilter(source,track_id,status,search_term,order);
            });

            $('.unpublished_filter_button').click(function(e){
                var source        = $('#unpublished_event_type').val();
                var track_id      = $('#unpublished_track').val();
                var status        = $('#unpublished_selection_status').val();
                var search_term   = $('#unpublished_search_term').val();
                var order         = $('#unpublished_sort').val();

                if (!source) swal("Select a Source", "Please select a source to search on.", "warning");

                self.doFilter(source,track_id,status,search_term,order);
            });

            $("#unpublished_search_term").keydown(function (e) {
                if (e.keyCode == 13) {
                    $('#search_unpublished').click();
                }
            });

            self.doFilter('presentations', '','','','SummitEvent.Title');
        });

        doFilter(source, track_id, status, search_term, order)
        {
            $('body').ajax_loader();
            var page_size = $('#page-size').val();
            self.api.getUnpublishedEventsBySource(self.summit.id, source ,track_id, status, search_term, order, 1, page_size);
        }

        self.dispatcher.on(self.dispatcher.UNPUBLISHED_EVENTS_PAGE_CHANGED, function(page_nbr)
        {
            var source        = $('#unpublished_event_type').val();
            var track_id      = $('#unpublished_track').val();
            var status        = $('#unpublished_selection_status').val();
            var search_term   = $('#unpublished_search_term').val();
            var order         = $('#unpublished_sort').val();
            var page_size     = $('#page-size').val();

            self.api.getUnpublishedEventsBySource(self.summit.id, source ,track_id, status, search_term, order, page_nbr, page_size);
        });

        clearClicked(e){
            var source        = $('#unpublished_event_type').val();
            var track_id      = $('#unpublished_track').val();
            var status        = $('#unpublished_selection_status').val();
            var search_term   = $('#unpublished_search_term').val();
            var order         = $('#unpublished_sort').val();

            if (!source) swal("Select a Source", "Please select a source to search on.", "warning");

            self.doFilter(source,track_id,status,search_term,order);
        }

    </script>
</schedule-admin-view-unpublished-filters>