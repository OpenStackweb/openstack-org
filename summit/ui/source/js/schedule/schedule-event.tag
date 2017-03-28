<schedule-event>
    <div id="event_{ id }" class="row event-row">
        <div class="col-sm-12">
            <div class="row main-event-content row-eq-height" style="border-left: 3px solid { eventColor(category_group_ids, type_id) }" data-event-id="{ id }">
                <div class="event-content col-sm-10 col-xs-10">
                    <div class="row row_location">
                        <div class="col-sm-3 col-time">
                            <i class="fa fa-clock-o icon-clock"></i>
                            <span>{ date_nice }</span>
                            ,&nbsp;<span>{ start_time }</span>-<span>{ end_time }</span>
                        </div>
                        <div class="col-sm-9 col-location">
                            <div if={ summit.should_show_venues } >
                                <i class="fa fa-map-marker icon-map"></i>
                                &nbsp;
                                <a class="search-link" if={ summit.locations[location_id] } href="{ summit.locations[location_id].link }">
                                    { locationName(location_id) }
                                </a>
                                <span if={ !summit.locations[location_id] }> { locationName(location_id) } </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 event-title">
                            <raw content="{ title }"/>
                            <a class="attachment-link" href="{ attachment_url }" if={ attachment_url } ><i class="fa fa-download" aria-hidden="true"></i></a>
                            <span class="record-icon" if={ to_record }><i class="fa fa-video-camera" aria-hidden="true"></i></span>
                        </div>
                    </div>
                    <div class="row" if={ sponsors_id.length > 0 }>
                        <div class="col-md-12 col-xs-12 col-sponsors">
                            Sponsored by { sponsorNames(sponsors_id) }
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-7 col-track">
                            <span if={ track_id } class="track">
                                <a class="search-link" title="Search Track" href="{ parent.search_url+'?t='+encodeURIComponent(trackName().replace(/ /g,'+')) }">{ trackName() }</a>
                            </span>
                        </div>
                        <div class="col-xs-5 event-type-col">
                            <a class="search-link" title="Search Event Type" href="{ parent.search_url+'?t='+encodeURIComponent(summit.event_types[type_id].type.replace(/ /g,'+')) }">{ summit.event_types[type_id].type }</a>
                        </div>
                    </div>
                </div>
                <div id="{ 'event_state_'+id }" class="event-state col-sm-1 col-xs-1">
                    <i if={ self.summit.current_user != null && going } class="fa fa-check-circle going-status event-status" aria-hidden="true"></i>
                    <i if={ self.summit.current_user != null && !going && favorite } class="fa fa-bookmark favorite-status event-status" aria-hidden="true"></i>
                </div>
                <div class="event-actions-container col-sm-1 col-xs-1" data-event-id="{ id }" id="{ 'event_actions_'+ id }" onclick={ onOpenMenu }>
                    <a id="{ 'event_action_menu_'+ id }" class="event-actions-menu" data-target="#" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" title+"event actions">
                        <span class="caret caret-event-actions"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-event-actions" aria-labelledby="{ 'event_action_menu_'+ id }">
                       <li if={ !going && has_rsvp && self.summit.current_user.is_attendee } class="rsvp-action event-action"><a onclick={ onMenuItemSelected } data-event-id="{ id }" data-type="rsvp" class="event-action-link {  !going && has_rsvp && rsvp_seat_type == 'FULL' ? 'disabled' : ''}" href="#"><i class="fa fa-check-circle" aria-hidden="true"></i>&nbsp;RSVP</a></li>
                       <li if={ going && has_rsvp && !rsvp_external && self.summit.current_user.is_attendee } class="unrsvp-action event-action"><a onclick={ onMenuItemSelected } data-event-id="{ id }" data-type="unrsvp" class="event-action-link" href="#"><i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;unRSVP</a></li>
                       <li if={ !has_rsvp  && !going  && self.summit.current_user.is_attendee } class="going-action event-action"><a onclick={ onMenuItemSelected } data-event-id="{ id }" data-type="going" class="event-action-link" href="#"><i class="fa fa-check-circle" aria-hidden="true"></i>&nbsp;Schedule</a></li>
                       <li if={ ( !has_rsvp && going || ( has_rsvp && rsvp_external && going) ) && self.summit.current_user.is_attendee } class="not-going-action event-action"><a onclick={ onMenuItemSelected } data-event-id="{ id }" data-type="not-going" class="event-action-link" href="#"><i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;UnSchedule</a></li>
                       <li if={ !favorite } class="watch-action event-action"><a onclick={ onMenuItemSelected } data-event-id="{ id }" data-type="watch" class="event-action-link" href="#"><i class="fa fa-bookmark" aria-hidden="true"></i>&nbsp;Watch Later</a></li>
                       <li if={ favorite } class="unwatch-action event-action"><a onclick={ onMenuItemSelected } data-event-id="{ id }" data-type="unwatch" class="event-action-link" href="#"><i class="fa fa-bookmark-o" aria-hidden="true"></i>&nbsp;Do not Watch Later</a></li>
                       <li class="share-facebook-action event-action"><a onclick={ onMenuItemSelected } data-event-id="{ id }" data-type="share-facebook" class="event-action-link" href="#"><i class="fa fa-facebook-official" aria-hidden="true"></i>&nbsp;Share on Facebook</a></li>
                       <li class="share-twitter-action event-action"><a onclick={ onMenuItemSelected } data-event-id="{ id }" data-type="share-twitter" class="event-action-link" href="#"><i class="fa fa-twitter-square" aria-hidden="true"></i>&nbsp;Share on Twitter</a></li>
                       <li role="separator" class="divider"></li>
                       <li class="cancel-action event-action"><a onclick={ onMenuItemSelected } data-event-id="{ id }" data-type="cancel" class="event-action-link" href="#">Cancel</a></li>
                    </ul>
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

            $(document).off("click", ".main-event-content").on( "click", ".main-event-content", function(e) {

            if($(e.target).is('.icon-event-action, .event-action-link')){
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

    this.schedule_api.on('addedEvent2MySchedule', function(event_id){
        var event    = self.summit.dic_events[event_id];
        if(event.has_rsvp && event.rsvp_external){
            var url      = new URI(event.rsvp_link);
            url.addQuery('BackURL', window.location)
            window.location = url.toString();
        }
    });

    onOpenMenu(e){
         var item     = $(e.currentTarget);
         var event_id = item.data('event-id');
         e.preventDefault();
         e.stopPropagation();
         $('#event_action_menu_'+event_id).dropdown('toggle');
         return false;
    }

    onMenuItemSelected(e){
            var item     = $(e.currentTarget);
            if(item.hasClass('disabled') || item.parent().hasClass('disabled')) return false;

            var event_id = item.data('event-id');
            $('#event_action_menu_'+event_id).removeClass('open');
            var type     = item.data('type');
            var event    = self.summit.dic_events[event_id];
            switch(type){
                case 'going':
                self.schedule_api.addEvent2MySchedule(self.summit.id, event.id);
                event.going = self.going = true;
                break;
                case 'not-going':
                event.going = self.going = false;
                self.schedule_api.removeEventFromMySchedule(self.summit.id, event.id);
                break;
                case 'watch':
                self.schedule_api.addEvent2MyFavorites(self.summit.id, event.id);
                event.favorite = self.favorite = true;
                break;
                case 'unwatch':
                self.schedule_api.removeEventFromMyFavorites(self.summit.id, event.id);
                event.favorite = self.favorite = false;
                break;
                case 'rsvp':
                event.going = self.going = true;
                if(event.rsvp_external){
                self.schedule_api.addEvent2MySchedule(self.summit.id, event.id);
                }
                else
                {
                    // our custom one, just navigate
                    var url = new URI(event.rsvp_link);
                    url.addQuery('BackURL',window.location)
                    window.location = url.toString();
                }
                break;
                case 'unrsvp':
                self.going = event.going = false;
                self.schedule_api.unRSVPEvent(self.summit.id, event.id);
                break;
                case 'share-facebook':
                    FB.ui({
                        method: 'share',
                        href: event.url,
                    }, function(response){});
                break;
                case 'share-twitter':
                    var text = event.social_summary != '' ? event.social_summary : self.summit.share_info.tweet
                    window.open('https://twitter.com/intent/tweet?text='+text+'&url='+event.url, 'mywin','left=50,top=50,width=600,height=260,toolbar=1,resizable=0');
                break;
            }
    }

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

    </script>

</schedule-event>