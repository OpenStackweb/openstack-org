<schedule-main-filters>
    <div class="row all-events-filter-row">
        <div class="col-xs-3 all-events-filter-link">
            <div class="col-filter-btn">
                <i title="" data-placement="right" data-toggle="tooltip" id="toggle-all-events-filters" class="fa fa-filter" data-original-title="Toggle Advanced Filters"></i>
            </div>
            <div class="col-filter-title">
                <span>Calendar&nbsp;Search&nbsp;Filters</span>
                <span id="clear-filters" style="display:none">(<a onclick={ clearFilters }>clear</a>) </span>
            </div>
        </div>
        <div class="col-xs-3 col-switch-schedule">
            <button if={ summit.current_user !== null } type="button" class="btn btn-primary pull-right switch_schedule full"><span class="glyphicon glyphicon-calendar"></span>&nbsp;<span class="content">Switch to My Schedule</span></button>
        </div>
        <div class="col-xs-3 col-view-all-schedule">
            <a href="{ base_url+'mine/' }?goback=1" class="btn btn-default pull-right view-all-schedule" role="button" if={ mine }>View / Print Full Calendar</a>
        </div>
        <div class="col-xs-3 login-container" if={ summit.current_user == null }>
            <form id="MemberLoginForm_LoginForm" action="Security/login?BackURL={ base_url  }" method="post" enctype="application/x-www-form-urlencoded">
                <input type="hidden" name="fragment" id="fragment"/>
                <div class="Actions">
                    <input class="action btn btn-default" type="submit" name="action_dologin" value="Log in" title="Log in"/>
                </div>
            </form>
        </div>
    </div>
   <div id="all-events-filter-wrapper" class="row">
        <div class="col-sm-15 col-xs-12 single-filter-wrapper first">
            <select id="ddl_summit_types" name="ddl_summit_types" data-placeholder="Summit Type"  multiple="multiple">
                <option each={ id, obj in summit.summit_types } data-color="{ obj.color }" value="{ id }">{ obj.name }</option>
             </select>
        </div>
        <div class="col-sm-15 col-xs-12 single-filter-wrapper">
            <select id="ddl_event_types" name="ddl_event_types" data-placeholder="Event Type"  multiple="multiple">
                <option each={ id, obj in summit.event_types } value="{ id }">{ obj.type }</option>
            </select>
        </div>
        <div class="col-sm-15 col-xs-12 single-filter-wrapper">
            <select id="ddl_tracks" data-placeholder="Presentation Tracks"  multiple="multiple">
                <option each={ id, obj in summit.tracks } value="{ id }">{ obj.name }</option>
            </select>
        </div>
        <div class="col-sm-15 col-xs-12 single-filter-wrapper">
            <select id="ddl_levels" data-placeholder="Presentation Levels"  multiple="multiple">
                <option each={ id, obj in summit.presentation_levels } value="{ id }">{ obj.level }</option>
            </select>
        </div>
        <div class="col-sm-15 col-xs-12 single-filter-wrapper">
            <select id="ddl_tags" data-placeholder="Tags"  multiple="multiple">
                <option each={ id, obj in summit.tags } value="{ id }">{ obj.name }</option>
            </select>
        </div>
    </div>

    <script>

        this.summit           = opts.summit;
        this.schedule_filters = opts.schedule_filters;
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

            $('#ddl_summit_types').chosen({  width: '100%'});
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

            $(window).url_fragment('setParam','summit_types', filters.summit_types);
            $(window).url_fragment('setParam','event_types', filters.event_types);
            $(window).url_fragment('setParam','tracks', filters.tracks);
            $(window).url_fragment('setParam','tags', filters.tags);
            $(window).url_fragment('setParam','levels', filters.levels);
            window.location.hash = $(window).url_fragment('serialize');

            self.schedule_filters.publishFiltersChanged(filters);
        }

        clearFilters() {
            $('#ddl_summit_types').val('').trigger("chosen:updated");
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
