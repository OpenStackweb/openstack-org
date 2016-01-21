<schedule-admin-view-unpublished-filters>
    <div class="row filters">
        <div class="col-md-6">
            <div>
                <label for="select_unpublished_events_source">Source</label>
                <select id="select_unpublished_events_source" name="select_unpublished_events_source" style="width: 70%">
                    <option value=''>-- Select An Event Source --</option>
                    <option value='tracks'>Tracks List</option>
                    <option value='presentations'>Presentations</option>
                    <option value='events'>Summit Events</option>
                </select>
            </div>
            <div id="track_list_col" style="display:none;">
                <label for="select_track_list">Track Lists</label>
                <select id="select_track_list" name="select_track_list" style="width: 70%">
                    <option value=''>-- All --</option>
                    <option each="{ id, list in summit.track_lists }" value='{ id }'>{ list.name }</option>
                </select>
            </div>
            <div>
                <label for="sort_list">Sort by</label>
                <select id="sort_list" name="sort_list" style="width: 70%" value="title">
                    <option value='Title' selected>Title</option>
                    <option value='SummitEvent.ID'>Event Id</option>
                    <option value='StartDate'>Start Date</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group" style="width: 100%;">
                <input data-rule-required="true" data-rule-minlength="3" type="text" id="search_term" name="search_term" class="form-control input-global-search" placeholder="Search for Events">
                <span class="input-group-btn" style="width: 5%;">
                    <button class="btn btn-default btn-global-search" type="submit"><i class="fa fa-search"></i></button>
                    <button class="btn btn-default btn-global-search-clear" onclick={ clearClicked }>
                        <i class="fa fa-times"></i>
                    </button>
                </span>
            </div>
        </div>
    </div>

    <div class="row refresh_button_div">
        <div class="col-md-12">
            <button class="btn btn-primary btn-sm unpublished-events-refresh" title="refresh unpublished events">
                &nbsp;Refresh&nbsp;<i class="fa fa-refresh"></i>
            </button>
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
                    var search_term = $('#search_term').val();
                    var order = $('#sort_list').val();

                    self.dispatcher.unpublishedEventsSourceChanged(source);
                    if(source === '') {
                        $('#track_list_col').hide();
                        return;
                    }
                    if(source === 'tracks') $('#track_list_col').show();
                    else $('#track_list_col').hide();

                    self.doFilter(source,'',search_term,order);
                });

                $('#select_track_list').change(function(e){
                    var source       = $('#select_unpublished_events_source').val();
                    var track_list_id = $('#select_track_list').val();
                    var search_term = $('#search_term').val();
                    var order = $('#sort_list').val();

                    self.doFilter(source, track_list_id,search_term,order);
                });

                $('.unpublished-events-refresh').click(function(e){
                    var source       = $('#select_unpublished_events_source').val();
                    var track_list_id = $('#select_track_list').val();
                    var search_term = $('#search_term').val();
                    var order = $('#sort_list').val();

                    if (source) {
                        self.doFilter(source, track_list_id,search_term,order);
                    }
                });

                $('#sort_list').change(function(e) {
                    var source       = $('#select_unpublished_events_source').val();
                    var track_list_id = $('#select_track_list').val();
                    var search_term = $('#search_term').val();
                    var order = $('#sort_list').val();

                    if (source) {
                        self.doFilter(source, track_list_id,search_term,order);
                    }
                });

                $('.btn-global-search').click(function(e) {
                    var source       = $('#select_unpublished_events_source').val();
                    var track_list_id = $('#select_track_list').val();
                    var search_term = $('#search_term').val();
                    var order = $('#sort_list').val();

                    if (source) {
                        self.doFilter(source, track_list_id,search_term,order);
                    } else {
                        alert("Please select a source to search on.");
                    }
                });

                $("#search_term").keydown(function (e) {
                    if (e.keyCode == 13) {
                        $('.btn-global-search').click();
                    }
                });
            });
        });

        doFilter(source, track_list_id, search_term, order) {
            $('body').ajax_loader();
            self.api.getUnpublishedEventsBySource(self.summit.id, source ,track_list_id, search_term, order);
        }

        self.dispatcher.on(self.dispatcher.UNPUBLISHED_EVENTS_PAGE_CHANGED, function(page_nbr)
        {
            var source       = $('#select_unpublished_events_source').val();
            var track_list_id = $('#select_track_list').val();
            var search_term = $('#search_term').val();
            var order = $('#sort_list').val();

            self.api.getUnpublishedEventsBySource(self.summit.id, source ,track_list_id, search_term, order, page_nbr, 10);
        });

        clearClicked(e){

            $('#search_term').val('');

            var source       = $('#select_unpublished_events_source').val();
            var track_list_id = $('#select_track_list').val();
            var search_term = $('#search_term').val();
            var order = $('#sort_list').val();

            if (source) {
                self.doFilter(source, track_list_id,search_term,order);
            }
        }

    </script>
</schedule-admin-view-unpublished-filters>