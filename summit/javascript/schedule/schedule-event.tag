<schedule-event>
    <div if={ show } id="event_{ id }" class="row event-row">
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
                                <a class="search-link" if={ summit.locations[location_id] } href="{ summit.link+'venues/#venue='+ summit.locations[location_id].venue_id }">
                                    { locationName(location_id) }
                                </a>
                                <span if={ !summit.locations[location_id] }> { locationName(location_id) } </span>
                            </div>
                        </div>
                        <div class="col-sm-3 my-schedule-container" if={ parent.summit.current_user !== null } >
                            <span if={ !own } onclick={ addToMySchedule } title="add to my schedule" class="icon-event-action">
                                <i class="fa fa-plus-circle icon-foreign-event myschedule-icon" ></i>
                                My&nbsp;calendar
                            </span>
                            <span if={ own } onclick={ removeFromMySchedule } title="remove from my schedule" class="icon-event-action">
                                <i class="fa fa-check-circle icon-own-event myschedule-icon"></i>
                                My&nbsp;calendar
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 event-title">
                            <raw content="{ title }"/>
                        </div>
                    </div>
                    <div class="row" if={ sponsors_id.length > 0 }>
                        <div class="col-md-12 col-sponsors">
                            Sponsored by { sponsorNames(sponsors_id) }
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-8 col-track">
                            <span if={ track_id} class="track">
                                <a class="search-link" title="Search Track" href="{ parent.search_url+'?t='+trackName().replace(/ /g,'+') }">{ trackName() }</a>
                            </span>
                        </div>
                        <div class="col-xs-4 event-type-col">
                            <a class="search-link" title="Search Event Type" href="{ parent.search_url+'?t='+summit.event_types[type_id].type.replace(/ /g,'+') }">{ summit.event_types[type_id].type }</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row event-details" id="event_details_{ id }" style="display:none;">
                <div class="row">
                    <div class="col-sm-8">
                        <div class="bio-row">
                            <raw content="{ abstract }"/>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div data-speaker-id={ speaker_id } class="row speaker-row" each={ speaker_id in speakers_id }>
                            <div class="speaker-name-row">
                                <div class="col-sm-12">
                                    <div class="speaker-photo-left">
                                        <a href={ parent.base_url+'speakers/'+ this.speaker_id } class="profile-pic-wrapper" style="background-image: url('{ summit.speakers[speaker_id].profile_pic }')">
                                        </a>
                                    </div>
                                    <div class="speaker-name-right">
                                        <a href={ parent.base_url+'speakers/'+ this.speaker_id }>
                                            { summit.speakers[speaker_id].name }
                                        </a>
                                        <div class="speaker-company">
                                            { summit.speakers[speaker_id].position }
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <div class="space-row">
                                &nbsp;
                            </div>
                            <div class="level-row" if={ level }>
                                <div class="col-sm-12 col-level-content">
                                    <i class="fa fa-signal level-icon"></i>
                                    <span>Level:</span>
                                    <span class="presentation-level'">
                                        <a class="search-link" title="Search Presentation Level" href="{ parent.search_url+'?t='+level }">{ level }</a>
                                    </span>
                                </div>
                            </div>
                            <div class="tags-row" if={ tags_id.length > 0 }>
                                <div class="col-sm-12 col-tags-content">
                                    <i class="fa fa-tags"></i>
                                    <span>Tags:</span>
                                    <span each={ tag_id, i in tags_id } title="Search Tag" class="tag">
                                        <a class="search-link" href="{ parent.search_url+'?t='+summit.tags[tag_id].name.replace(/ /g,'+') }">{ summit.tags[tag_id].name+ ( (i < parent.tags_id.length - 1) ? ', ':'' ) }</a>
                                        &nbsp;
                                    </span>
                                </div>
                            </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="event-btn">
                            <form action="{ parent.base_url+'events/'+ id }" method="POST">
                                <input type="hidden" name="goback" value="1" />
                                <button type="submit" class="btn btn-primary btn-md active btn-warning btn-go-event" role="button">EVENT DETAILS</button>
                            </form>
                        </div>
                        <div class="event-btn" if={ rsvp_link != null && rsvp_link != ''}>
                            <a href={ rsvp_link } class="btn btn-primary btn-md active btn-warning btn-rsvp-event" target="_blank" role="button">RSVP to this Event</a>
                        </div>
                    </div>
                </div>
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

    // show event details handler (jquery)
    $(document).off("click", ".main-event-content").on( "click", ".main-event-content", function(e) {

            if($(e.target).is('.icon-event-action, .myschedule-icon')){
                return false;
            }

            if($(e.target).hasClass('search-link')) return true;

            var event_id = $(e.currentTarget).attr('data-event-id');
            var detail   = $('#event_details_'+event_id);

            if ( detail.is( ":hidden" ) ) {
                detail.slideDown( "slow" );
            } else {
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
        if(location.class_name === 'SummitVenueRoom') {
        var room = location;
            location = self.summit.locations[room.venue_id];
            return location.name+' - '+room.name;
        }
        return location.name;
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

    hasDesignCredentials(summit_types_id) {
        for(var id of summit_types_id)
        {
            var t = self.summit.summit_types[id];
            if(t.type === 'DESIGN') return true;
        }
        return false;
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
        console.log('EVENT.addToMySchedule');
        self.schedule_api.addEvent2MySchedule(self.summit.id, e.item.id);
        e.item.own = true;
    }

    removeFromMySchedule(e) {
        console.log('EVENT.removeFromMySchedule');
        self.schedule_api.removeEventFromMySchedule(self.summit.id, e.item.id);
        e.item.own = false;
    }

    </script>

</schedule-event>