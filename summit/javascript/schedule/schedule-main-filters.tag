<schedule-main-filters>
    <div class="row all-events-filter-row">
        <div class="col-xs-12 col-md-12 all-events-filter-container">
           <div class="row">
                <div class="col-xs-12 col-md-2 all-events-filter-link">
                    <div class="row">
                        <div class="col-xs-1 col-md-3 col-filter-btn"><i title="" data-placement="right" data-toggle="tooltip" id="toggle-all-events-filters" class="fa fa-filter" data-original-title="Toggle Advanced Filters"></i></div>
                        <div class="col-xs-11 col-md-9 col-filter-title"><span>Filter&nbsp;View</span></div>
                    </div>
                </div>
            </div>
           <div id="all-events-filter-wrapper" class="row">
                <div class="col-xs-12 col-md-12">
                    <div class="row">
                                    <div class="col-xs-12 col-sm-3 single-filter-wrapper first">
                                        <select id="ddl_summit_types" name="ddl_summit_types" data-placeholder="Choose Summit Type ..."  multiple="multiple">
                                            <option each={ id, obj in summit.summit_types } data-color="{ obj.color }" value="{ id }">{ obj.type }</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-12 col-sm-3 single-filter-wrapper">
                                        <select id="ddl_event_types" name="ddl_event_types" data-placeholder="Choose Event Type ..."  multiple="multiple">
                                            <option each={ id, obj in summit.event_types } value="{ id }">{ obj.type }</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-12 col-sm-3 single-filter-wrapper">
                                        <select id="ddl_tracks" data-placeholder="Choose Tracks ..."  multiple="multiple">
                                            <option each={ id, obj in summit.tracks } value="{ id }">{ obj.name }</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-12 col-sm-3 single-filter-wrapper">
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


            $('#ddl_summit_types').change(function(e, params){
                var filters = {
                    summit_types : $('#ddl_summit_types').val(),
                    event_types  : $('#ddl_event_types').val(),
                    tracks       : $('#ddl_tracks').val(),
                    tags         : $('#ddl_tags').val()
                };
                self.schedule_filters.publishFiltersChanged(filters);
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
                var filters = {
                    summit_types : $('#ddl_summit_types').val(),
                    event_types  : $('#ddl_event_types').val(),
                    tracks       : $('#ddl_tracks').val(),
                    tags         : $('#ddl_tags').val()
                };
                self.schedule_filters.publishFiltersChanged(filters);
            });

            $('#ddl_tracks').chosen().change(function(e){
                var filters = {
                    summit_types : $('#ddl_summit_types').val(),
                    event_types : $('#ddl_event_types').val(),
                    tracks      : $('#ddl_tracks').val(),
                    tags        : $('#ddl_tags').val()
                };
                self.schedule_filters.publishFiltersChanged(filters);
            });

            $('#ddl_tags').chosen().change(function(e){
                var filters = {
                    summit_types : $('#ddl_summit_types').val(),
                    event_types : $('#ddl_event_types').val(),
                    tracks      : $('#ddl_tracks').val(),
                    tags        : $('#ddl_tags').val()
                };
                self.schedule_filters.publishFiltersChanged(filters);
            });
       });


    </script>
</schedule-main-filters>