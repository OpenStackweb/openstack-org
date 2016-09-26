<schedule-main-filters>
    <div class="row all-events-filter-row">
        <div class="col-md-4 col-xs-12 all-events-filter-link">
            <div class="col-filter-btn">
                <i title="" data-placement="right" data-toggle="tooltip" id="toggle-all-events-filters" class="fa fa-filter" data-original-title="Toggle Advanced Filters"></i>
            </div>
            <div class="col-filter-title">
                <span>Schedule&nbsp;Filters</span>
                <a onclick={ clearFilters } id="clear-filters">CLEAR&nbsp;FILTERS&nbsp;<i class="fa fa-times"></i></a>
            </div>
        </div>
        <div class="col-md-7 col-xs-12">

            <div class="col-view-all-schedule">
                <form action="{ base_url+'mine/' }" method="POST" if={ mine }>
                    <input type="hidden" name="goback" value="1" />
                    <button type="submit" class="btn btn-default view-all-schedule">View&nbsp;/&nbsp;Print&nbsp;My&nbsp;Schedule</button>
                </form>
                <form action="{ base_url+'full/' }" method="POST" if={ !mine }>
                    <input type="hidden" name="goback" value="1" />
                    <button type="submit" class="btn btn-default view-all-schedule">View&nbsp;/&nbsp;Print&nbsp;Full&nbsp;Schedule</button>
                </form>
            </div>
            <div class="col-select-all-calendar-own" if={ mine }>
                <input type="checkbox" id="chk_select_all" title="select/unselect all events"/>
            </div>
            <div class="col-sync-calendar-own" if={ mine }>
                <div class="btn-group">
                    <button type="button" class="btn btn-default">Sync to Calendar</button>
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a data-target="#" class="link-google-sync" id="link_google_sync"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>&nbsp;Google&nbsp;sync</a></li>
                        <li><a data-target="#" class="link-google-unsync" id="link_google_unsync"><i class="fa fa-calendar-times-o" aria-hidden="true"></i>&nbsp;Google&nbsp;unsync</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a data-target="#" class="link-export-ics" id="link_export_ics"><span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span>&nbsp;Export&nbsp;ICS</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-switch-schedule">
                <button if={ summit.current_user !== null } type="button" class="btn btn-primary pull-right switch_schedule full">
                    <span class="glyphicon glyphicon-calendar"></span>&nbsp;<span class="content">Switch&nbsp;to&nbsp;My&nbsp;Schedule</span>
                </button>
            </div>
        </div>
    </div>
    <div class="rsvp-note" if={ summit.current_user !== null }>
        Please note that adding an item to "My Schedule" does not guarantee a seat in the presentation.
        Rooms fill up fast, so get there early. Some events require an RSVP and, in those cases, you will see a link to RSVP to the event.
    </div>
   <div id="all-events-filter-wrapper" class="row">
        <div class="col-sm-12">
            <a href="{ summit.track_list_link }" target="_blank">Learn more about the { summit.title } Summit Categories and Tracks.</a>
        </div>
        <div class="col-sm-15 col-xs-12 single-filter-wrapper first">
            <select id="ddl_summit_types" name="ddl_summit_types" data-placeholder="Summit Type"  multiple="multiple">
                <option each={ id, obj in summit.summit_types } data-color="{ obj.color }" value="{ id }">{ obj.name }</option>
            </select>
        </div>
        <div class="col-sm-15 col-xs-12 single-filter-wrapper">
            <select id="ddl_track_groups" name="ddl_track_groups" data-placeholder="Summit Categories" size="5"  multiple="multiple">
                <option each={ track_group_id, idx in summit.category_group_ids } value="{ track_group_id }">{ summit.category_groups[track_group_id].name }</option>
             </select>
        </div>
         <div class="col-sm-15 col-xs-12 single-filter-wrapper">
            <select id="ddl_tracks" data-placeholder="Tracks"  multiple="multiple" style="overflow-y: scroll;">
                <option each={ track_id, idx in summit.track_ids } value="{ track_id }">{ summit.tracks[track_id].name }</option>
            </select>
        </div>
        <div class="col-sm-15 col-xs-12 single-filter-wrapper" style="display:none;">
            <select id="ddl_event_types" name="ddl_event_types" data-placeholder="Event Types" size="7" multiple="multiple">
                <option each={ event_type_id, idx in summit.event_type_ids } value="{ event_type_id }">{ summit.event_types[event_type_id].type }</option>
            </select>
        </div>
        <div class="col-sm-15 col-xs-12 single-filter-wrapper">
            <select id="ddl_levels" data-placeholder="Presentation Level"  multiple="multiple">
                <option each={ id, obj in summit.presentation_levels } value="{ id }">{ obj.level }</option>
            </select>
        </div>
        <div class="col-sm-15 col-xs-12 single-filter-wrapper">
            <select id="ddl_tags" data-placeholder="Tags"  multiple="multiple" style="overflow-y: scroll;">
                <option each={ tag_id, idx in summit.tag_ids } value="{ tag_id }">{ summit.tags[tag_id].name }</option>
            </select>
        </div>
    </div>

    <script>

        this.summit           = opts.summit;
        this.schedule_filters = opts.schedule_filters;
        this.calendar_synch   = opts.calendar_synch;
        this.atomic_filtering = false;
        this.base_url         = opts.base_url;
        this.mine             = false;
        var self              = this;

        this.on('mount', function(){
            // Tooltips
            if(!('ontouchstart' in window)) {
                $('[data-toggle="tooltip"]').tooltip();
            }
            // Toggle All Events Filters
            $('#toggle-all-events-filters').click(function(event) {
                if ( $('#all-events-filter-wrapper').is( ":hidden" ) ) {
                    $('#all-events-filter-wrapper').slideDown( "slow" );
                    $('#clear-filters').show();
                } else {
                    $('#all-events-filter-wrapper').slideUp( "slow" );
                    $('#clear-filters').hide();
                }
                $(this).toggleClass('active');
                event.preventDefault();
            });

            $(document).on('click', '#chk_select_all' , function(){
                var visible_checkboxes = $(".select-event-chk:visible");
                console.log('checked '+visible_checkboxes.length+' events');
                visible_checkboxes.prop('checked', $(this).is(':checked'));
            });

            $('#ddl_summit_types').chosen({  width: '100%'});
            $('#ddl_track_groups').chosen({  width: '100%'});
            $('#ddl_event_types').chosen({  width: '100%'});
            $('#ddl_tracks').chosen({  width: '100%'});
            $('#ddl_tags').chosen({ width: '100%'});
            $('#ddl_levels').chosen({ width: '100%'});


            $('#ddl_summit_types').change(function(e, params){
                if(!self.atomic_filtering)
                    self.doFilter();
                var choices = $('.search-choice','#ddl_summit_types_chosen');
                if(choices.length > 0){
                    choices.each(function(index, e){
                        var a      = $('.search-choice-close', $(this));
                        var idx    = $(a).attr('data-option-array-index');
                        var option = $("#ddl_summit_types option")[parseInt(idx)];
                        var color  = $(option).attr('data-color');
                        $(this).css('background-color', color);
                        $(this).css('background-image','none');
                    });
                }
            });

            $('#ddl_track_groups').change(function(e, params){

                var choices          = $('.search-choice','#ddl_track_groups_chosen');
                var filtered_tracks  = [];
                var selected_groups  = $('#ddl_track_groups').val();
                //clear current track filter
                $('#ddl_tracks').val('').trigger('chosen:updated');
                $(window).url_fragment('setParam','tracks', '');
                window.location.hash = $(window).url_fragment('serialize');
                $("#ddl_tracks option").show();

                if(choices.length > 0){
                    choices.each(function(index, e){
                        var a            = $('.search-choice-close', $(this));
                        var idx          = $(a).attr('data-option-array-index');
                        var option       = $("#ddl_track_groups option")[parseInt(idx)];
                        var color        = $(option).attr('data-color');
                        $(this).css('background-color', color);
                        $(this).css('background-image','none');
                    });
                }

                // get the groups
                if(selected_groups != null)
                    $.each(selected_groups, function(index, group_id){
                           var group        = self.summit.category_groups[group_id];
                           filtered_tracks  = filtered_tracks.concat(group.tracks);
                    });

                if(filtered_tracks.length > 0){
                    $.each(self.summit.tracks, function(index, track){
                        if(filtered_tracks.indexOf(track.id) < 0)
                            $('#ddl_tracks').children("option[value="+track.id+"]").hide();
                     });
                }

                $('#ddl_tracks').trigger('chosen:updated');

                 if(!self.atomic_filtering)
                    self.doFilter();
            });

            $('#ddl_event_types').change(function(e, params){
                if(!self.atomic_filtering)
                    self.doFilter();
            });

            $('#ddl_tracks').chosen().change(function(e){
                if(!self.atomic_filtering)
                    self.doFilter();
            });

            $('#ddl_tags').chosen().change(function(e){
                if(!self.atomic_filtering)
                    self.doFilter();
            });

            $('#ddl_levels').chosen().change(function(e){
                if(!self.atomic_filtering)
                    self.doFilter();
            });

            $('.switch_schedule').click(function(e){
                if ($(this).hasClass('full'))
                {
                    self.mine = true;
                    $('.content', this).text('Switch to Full Schedule');
                }
                else
                {
                    self.mine = false;
                    $('.content', this).text('Switch to My Schedule');
                }
                $(this).toggleClass('full');
                if(!self.atomic_filtering)
                    self.doFilter();

                self.update();
            });

            var hash = $(window).url_fragment('getParams');

            if(hash){
                // process local filters on hash ...

                self.atomic_filtering = true;
                for(var key in hash) {

                    var values = hash[key]
                    var ddl    = null;

                    switch(key) {
                        case 'summit_types':
                        {
                            ddl = $('#ddl_summit_types');
                        }
                        break;
                        case 'track_groups':
                        {
                            ddl = $('#ddl_track_groups');
                        }
                        break;
                        case 'event_types':
                        {
                            ddl = $('#ddl_event_types');
                        }
                        break;
                        case 'tracks':
                        {
                            ddl = $('#ddl_tracks');
                        }
                        break;
                        case 'tags':
                        {
                            ddl = $('#ddl_tags');
                        }
                        break;
                        case 'levels':
                        {
                            ddl = $('#ddl_levels');
                        }
                        break;
                    }
                    if(ddl == null) continue;

                    ddl.val(values.split(','));
                    ddl.trigger("chosen:updated").trigger("change");
                    $('#toggle-all-events-filters').click();
                }

                self.atomic_filtering = false;
                self.doFilter();
            }
        });

        doFilter() {
            var own     = this.summit.current_user !== null && $('.switch_schedule').hasClass('full') === false;
            var filters =
            {
                summit_types : $('#ddl_summit_types').val(),
                track_groups : $('#ddl_track_groups').val(),
                event_types  : $('#ddl_event_types').val(),
                tracks       : $('#ddl_tracks').val(),
                tags         : $('#ddl_tags').val(),
                levels       : $('#ddl_levels').val(),
                own          : own
            };

            $(window).url_fragment('setParam','summit_types', filters.summit_types);
            $(window).url_fragment('setParam','track_groups', filters.track_groups);
            $(window).url_fragment('setParam','event_types', filters.event_types);
            $(window).url_fragment('setParam','tracks', filters.tracks);
            $(window).url_fragment('setParam','tags', filters.tags);
            $(window).url_fragment('setParam','levels', filters.levels);
            window.location.hash = $(window).url_fragment('serialize');

            self.schedule_filters.publishFiltersChanged(filters);
        }

        clearFilters() {
            $('#ddl_summit_types').val('').trigger("chosen:updated");
            $('#ddl_track_groups').val('').trigger("chosen:updated");
            $('#ddl_event_types').val('').trigger("chosen:updated");
            $('#ddl_tracks').val('').trigger("chosen:updated");
            $('#ddl_tags').val('').trigger("chosen:updated");
            $('#ddl_levels').val('').trigger("chosen:updated");
            self.doFilter();
        }

        this.schedule_filters.on('scheduleToggleFilters', function(hide){
            if (hide) {
                $('.all-events-filter-link').fadeOut();
                $('#all-events-filter-wrapper').slideUp();
                $('#toggle-all-events-filters').removeClass('active');
                self.clearFilters();
            } else {
                $('.all-events-filter-link').fadeIn();
            }
        });

    </script>
</schedule-main-filters>
