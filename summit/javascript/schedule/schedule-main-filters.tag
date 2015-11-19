<schedule-main-filters>
    <div class="row all-events-filter-row">
        <div class="col-md-12 all-events-filter-container">
            <form class="all-events-search-form">
                <div class="col-sm-8 col-xs-8 all-events-search-wrapper">
                    <input type="search" placeholder="Search for Events/Speakers ..." id="all-events-search" onkeyup={ doFreeTextSearch }>
                    <i class="fa fa-search"></i>
                </div>
                <div class="col-xs-1 all-events-filter-link">
                    <i title="" data-placement="right" data-toggle="tooltip" id="toggle-all-events-filters" class="fa fa-filter" data-original-title="Toggle Advanced Filters"></i>
                </div>
                <div id="all-events-filter-wrapper" class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="checkbox checkbox-success checkbox-inline" each={ id, obj in summit.summit_types }>
                                    <input type="checkbox" checked="" value="{ id }" id="summit_type_{ id }" class="styled">
                                    <label for="summit_type_{ id }">{ obj.type }</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3 single-filter-wrapper first">
                                <select multiple id="ddl_event_types" data-placeholder="Choose Event Type ...">
                                <option each={ id, obj in summit.event_types } value="{ id }">{ obj.type }</option>
                                </select>
                            </div>
                            <div class="col-sm-3 single-filter-wrapper">
                                <select multiple id="ddl_tracks" data-placeholder="Choose Tracks ...">
                                <option each={ id, obj in summit.tracks } value="{ id }">{ obj.name }</option>
                                </select>
                            </div>
                            <div class="col-sm-3 single-filter-wrapper">
                                <select multiple id="ddl_tags" data-placeholder="Choose Tags ...">
                                <option each={ id, obj in summit.tags } value="{ id }">{ obj.name }</option>
                                </select>
                            </div>
                        </div>
                    </div>
            </div>
            </form>
        </div>
    </div>

    <script>
        this.summit = opts.summit;
        this.api    = opts.api;
        var self    = this;

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
       });

        doFreeTextSearch(e) {
            console.log('doFreeTextSearch');
        }
    </script>
</schedule-main-filters>