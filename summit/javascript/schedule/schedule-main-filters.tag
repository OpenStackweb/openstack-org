<schedule-main-filters>
    <div class="row all-events-filter-row">
        <div class="col-xs-12 col-md-12 all-events-filter-container">
           <div class="row">
                <div class="col-xs-12 col-md-6 all-events-filter-link">
                    <div class="row">
                        <div class="col-xs-1 col-md-1 col-filter-btn">
                            <i title="" data-placement="right" data-toggle="tooltip" id="toggle-all-events-filters" class="fa fa-filter" data-original-title="Toggle Advanced Filters"></i>
                        </div>
                        <div class="col-xs-4 col-md-2 col-filter-title">
                            <span>Filter&nbsp;View</span>
                        </div>
                        <div class="col-xs-7 col-md-9">
                            <button if={ summit.current_user !== null } type="button" class="btn btn-primary pull-left switch_schedule full"><span class="glyphicon glyphicon-calendar"></span>&nbsp;<span class="content">Switch to My Schedule</span></button>
                        </div>
                    </div>
                </div>
            </div>
           <div id="all-events-filter-wrapper" class="row">
                <div class="col-xs-12 col-md-12">
                    <div class="row">
                        <div class="col-xs-12 col-sm-2 single-filter-wrapper first">
                            <select id="ddl_summit_types" name="ddl_summit_types" data-placeholder="Choose Summit Type ..."  multiple="multiple">
                                <option each={ id, obj in summit.summit_types } data-color="{ obj.color }" value="{ id }">{ obj.name }</option>
                             </select>
                        </div>
                        <div class="col-xs-12 col-sm-2 single-filter-wrapper">
                            <select id="ddl_event_types" name="ddl_event_types" data-placeholder="Choose Event Type ..."  multiple="multiple">
                                <option each={ id, obj in summit.event_types } value="{ id }">{ obj.type }</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-sm-3 single-filter-wrapper">
                            <select id="ddl_tracks" data-placeholder="Choose Presentation Tracks ..."  multiple="multiple">
                                <option each={ id, obj in summit.tracks } value="{ id }">{ obj.name }</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-sm-3 single-filter-wrapper">
                            <select id="ddl_levels" data-placeholder="Choose Presentation Levels ..."  multiple="multiple">
                                <option each={ id, obj in summit.presentation_levels } value="{ id }">{ obj.level }</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-sm-2 single-filter-wrapper">
                            <select id="ddl_tags" data-placeholder="Choose Tags ..."  multiple="multiple">
                                <option each={ id, obj in summit.tags } value="{ id }">{ obj.name }</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

        this.summit           = opts.summit;
        this.schedule_filters = opts.schedule_filters;
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
                } else {
                    $('#all-events-filter-wrapper').slideUp( "slow" );
                }
                $(this).toggleClass('active');
                event.preventDefault();
            });

            $('#ddl_summit_types').chosen({  width: '100%'});
            $('#ddl_event_types').chosen({  width: '100%'});
            $('#ddl_tracks').chosen({  width: '100%'});
            $('#ddl_tags').chosen({ width: '100%'});
            $('#ddl_levels').chosen({ width: '100%'});

            $('#ddl_summit_types').change(function(e, params){

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
                        $(this).css('color','#FFFFFF');
                    });
                }
            });

            $('#ddl_event_types').change(function(e, params){
                self.doFilter();
            });

            $('#ddl_tracks').chosen().change(function(e){
                self.doFilter();
            });

            $('#ddl_tags').chosen().change(function(e){
                self.doFilter();
            });

            $('#ddl_levels').chosen().change(function(e){
                self.doFilter();
            });

            $('.switch_schedule').click(function(e){
                var mine = false;
                if ($(this).hasClass('full'))
                {
                    mine = true;
                    $('.content', this).text('Switch to Full Schedule');
                }
                else
                {
                    $('.content', this).text('Switch to My Schedule');
                }
                $(this).toggleClass('full');
                self.doFilter();
            });

            if(window.location.hash){
                // process local filters on hash ...
                var hash   = window.location.hash.substr(1);
                var params = hash.split('&');

                for(var param of params) {

                    var param = param.split('=');
                    if(param.length != 2) continue;
                    var ddl = null;

                    switch(param[0].toLowerCase()) {
                        case 'summit_types':
                        {
                            ddl = $('#ddl_summit_types');
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
                    if(ddl === null) continue;
                    var values = param[1].trim();
                    if(values === '') continue;

                    for(var val of values.split(',')) {
                        console.log('val '+val);
                        $('option', ddl).filter(function() {
                            return $(this).text() == val.trim();
                        }).prop('selected', true);
                    }
                    ddl.trigger("chosen:updated").trigger("change");
                }
            }
        });

        doFilter() {
            var own    = this.summit.current_user !== null && $('.switch_schedule').hasClass('full') === false;
            var filters =
            {
                summit_types : $('#ddl_summit_types').val(),
                event_types  : $('#ddl_event_types').val(),
                tracks       : $('#ddl_tracks').val(),
                tags         : $('#ddl_tags').val(),
                levels       : $('#ddl_levels').val(),
                own          : own
            };
            self.schedule_filters.publishFiltersChanged(filters);
        }

    </script>
</schedule-main-filters>