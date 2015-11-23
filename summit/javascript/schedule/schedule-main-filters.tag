<schedule-main-filters>
    <div class="row all-events-filter-row">
        <div class="col-md-12 all-events-filter-container">
           <div class="row">
                <div class="col-xs-1 all-events-filter-link">
                    <i title="" data-placement="right" data-toggle="tooltip" id="toggle-all-events-filters" class="fa fa-filter" data-original-title="Toggle Advanced Filters"></i>
                </div>
            </div>
            <div id="all-events-filter-wrapper" class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3" each={ id, obj in summit.summit_types }>
                                    <div class="row">
                                        <div class="col-md-11">
                                            <div class="checkbox checkbox-success checkbox-inline">
                                                <input type="checkbox" checked="" value="{ id }" id="summit_type_{ id }" class="styled summit_type_checkbox">
                                                <label for="summit_type_{ id }">{ obj.type }</label>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <i title="" data-placement="right" data-toggle="tooltip" class="fa fa-circle" data-original-title="{ obj.type }" style="color: { obj.color }"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3 single-filter-wrapper first">
                                    <select id="ddl_event_types" name="ddl_event_types" data-placeholder="Choose Event Type ..."  multiple="multiple">
                                    <option each={ id, obj in summit.event_types } value="{ id }">{ obj.type }</option>
                                    </select>
                                </div>
                                <div class="col-sm-3 single-filter-wrapper">
                                    <select id="ddl_tracks" data-placeholder="Choose Tracks ..."  multiple="multiple">
                                    <option each={ id, obj in summit.tracks } value="{ id }">{ obj.name }</option>
                                    </select>
                                </div>
                                <div class="col-sm-3 single-filter-wrapper">
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

            $('#ddl_event_types').chosen({  width: '100%'});
            $('#ddl_tracks').chosen({  width: '100%'});
            $('#ddl_tags').chosen({ width: '100%'});

            $('.summit_type_checkbox').click(function(e){
                var filters = {
                    summit_types : [],
                    event_types  : $('#ddl_event_types').val(),
                    tracks       : $('#ddl_tracks').val(),
                    tags         : $('#ddl_tags').val()
                };
                $('.summit_type_checkbox').each(function(index, e){
                    if($(this).is(':checked'))
                        filters.summit_types.push($(this).val());
                });
                self.schedule_filters.publishFiltersChanged(filters);
            });

            $('#ddl_event_types').change(function(e, params){
                var filters = {
                    summit_types : [],
                    event_types  : $('#ddl_event_types').val(),
                    tracks       : $('#ddl_tracks').val(),
                    tags         : $('#ddl_tags').val()
                };
                $('.summit_type_checkbox').each(function(index, e){
                    if($(this).is(':checked'))
                        filters.summit_types.push($(this).val());
                });
                self.schedule_filters.publishFiltersChanged(filters);
            });

            $('#ddl_tracks').chosen().change(function(e){
                var filters = {
                    summit_types : [],
                    event_types : $('#ddl_event_types').val(),
                    tracks      : $('#ddl_tracks').val(),
                    tags        : $('#ddl_tags').val()
                };
                $('.summit_type_checkbox').each(function(index, e){
                    if($(this).is(':checked'))
                        filters.summit_types.push($(this).val());
                });
                self.schedule_filters.publishFiltersChanged(filters);
            });

            $('#ddl_tags').chosen().change(function(e){
                var filters = {
                    summit_types : [],
                    event_types : $('#ddl_event_types').val(),
                    tracks      : $('#ddl_tracks').val(),
                    tags        : $('#ddl_tags').val()
                };
                $('.summit_type_checkbox').each(function(index, e){
                    if($(this).is(':checked'))
                        filters.summit_types.push($(this).val());
                });
                self.schedule_filters.publishFiltersChanged(filters);
            });
       });
    </script>
</schedule-main-filters>