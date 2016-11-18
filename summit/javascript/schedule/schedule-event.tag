<schedule-event>
    <div id="event_{ id }" class="row event-row">
        <div class="col-sm-12">
            <div class="row main-event-content" style="border-left: 3px solid { eventColor(category_group_ids, type_id) }" data-event-id="{ id }">
                <div class="event-content">
                    <div class="row row_location">
                        <div class="col-sm-3 col-time">
                            <i class="fa fa-clock-o icon-clock"></i>
                            <span if={ show_date }>{ date_nice }</span>
                            &nbsp;<span>{ start_time }</span>-<span>{ end_time }</span>
                        </div>
                        <div class="col-sm-6 col-location">
                            <div if={ summit.should_show_venues } >
                                <i class="fa fa-map-marker icon-map"></i>
                                &nbsp;
                                <a class="search-link" if={ summit.locations[location_id] } href="{ summit.locations[location_id].link }">
                                    { locationName(location_id) }
                                </a>
                                <span if={ !summit.locations[location_id] }> { locationName(location_id) } </span>
                            </div>
                        </div>
                        <div class="col-sm-3 my-schedule-container" if={ parent.summit.current_user !== null } >
                            <span if={ !own } onclick={ addToMySchedule } title="add to my schedule" class="icon-event-action">
                                <i class="fa fa-plus-circle icon-foreign-event myschedule-icon" ></i>
                                My&nbsp;schedule
                            </span>
                            <span if={ own } onclick={ removeFromMySchedule } title="remove from my schedule" class="icon-event-action">
                                <i class="fa fa-check-circle icon-own-event myschedule-icon"></i>
                                My&nbsp;schedule
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 event-title">
                            <raw content="{ title }"/>
                        </div>
                    </div>
                    <div class="row" if={ sponsors_id.length > 0 }>
                        <div class="col-md-12 col-xs-12 col-sponsors">
                            Sponsored by { sponsorNames(sponsors_id) }
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-7 col-track">
                            <span if={ track_id} class="track">
                                <a class="search-link" title="Search Track" href="{ parent.search_url+'?t='+encodeURIComponent(trackName().replace(/ /g,'+')) }">{ trackName() }</a>
                            </span>
                        </div>
                        <div class="col-xs-5 event-type-col">
                            <a class="search-link" title="Search Event Type" href="{ parent.search_url+'?t='+encodeURIComponent(summit.event_types[type_id].type.replace(/ /g,'+')) }">{ summit.event_types[type_id].type }</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row event-details" id="event_details_{ id }" style="display:none;">
            </div>
        </div>
    </div>

    <script>

    this.summit                   = this.parent.summit;
    this.search_url               = this.parent.search_url;
    this.schedule_api             = this.parent.schedule_api;
    this.default_event_color      = this.parent.default_event_color;
    this.show_date                = opts.show_date;
    var self                      = this;

    this.on('mount', function(){
    console.log("this.summit.should_show_venues "+this.summit.should_show_venues.should_show_venues);
    // show event details handler (jquery)
    $(document).off("click", ".main-event-content").on( "click", ".main-event-content", function(e) {

            if($(e.target).is('.icon-event-action, .myschedule-icon')){
                return false;
            }

            if($(e.target).hasClass('search-link')) return true;

            var event_id  = $(e.currentTarget).attr('data-event-id');
            var detail    = $('#event_details_'+event_id);
            var must_load = !detail.hasClass('loaded');
            if ( detail.is( ":hidden" ) ) {
                    detail.slideDown( "slow" );
                    if(must_load){
                        detail.html('loading ...');
                        var url =  self.parent.base_url+'events/'+ event_id+'/html'
                        $.ajax({
                        type: 'GET',
                        url:  url,
                        timeout:60000,
                        //ifModified: true,
                        //contentType: "application/json; charset=utf-8",
                        success: function (data, textStatus, jqXHR) {
                            detail.html(data);
                            detail.addClass('loaded');
                        }
                        }).fail(function (jqXHR, textStatus, errorThrown) {
                            alert('there was an error, please contact your administrator');
                        });
                    }
            }
            else
            {
                detail.slideUp( "slow" );
            }
            e.preventDefault();
            e.stopPropagation();
            return false;
        });
    });

    locationName(location_id) {
        var location = self.summit.locations[location_id];
        if (typeof location == 'undefined') return 'TBA';
        else return location.name_nice;
    }

    sponsorNames(sponsors_id) {
        var sponsors = '';
        for(var id of sponsors_id)
        {
            var s = self.summit.sponsors[id];
            if(sponsors !== '') sponsors += ', ';
            sponsors += s.name;
        }
        return sponsors;
    }

    eventColor(category_group_ids, type_id) {
        if(category_group_ids.length == 0){
            return self.summit.event_types[type_id].color
        }
        return self.summit.category_groups[category_group_ids[0]].color;
    }

    trackName(){
        var track_id = self.track_id;
        if(typeof track_id !== "undefined"){
            return self.summit.tracks[track_id].name;
        }
        return ' ';
    }

    //EVENTS

    addToMySchedule(e) {
        e.preventUpdate = true;
        this.own        = true;
        e.item.own      = true;
        self.schedule_api.addEvent2MySchedule(self.summit.id, e.item.id);
        self.update();
        self.parent.applyFilters();
    }

    removeFromMySchedule(e) {
        e.preventUpdate = true;
        this.own        = false;
        e.item.own      = false;
        self.schedule_api.removeEventFromMySchedule(self.summit.id, e.item.id);
        self.update();
        self.parent.applyFilters();
    }

    </script>

</schedule-event>