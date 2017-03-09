<schedule-grid-events>
    <style type="text/css">
        .modal-body .container {
            width: auto ;
        }
    </style>
    <div class="row">
        <div class="col-md-12 col-xs-12" id="events-inner-container">
        </div>
    </div>

    <div class="row" style="display:none;">
        <div class="col-md-12 col-xs-12">
            <p>* There are not events that match your search criteria. </p>
        </div>
    </div>

    <div class="row" id="no_events_msg" style="display:none;margin-top: 25px;">
        <div class="col-md-12 col-xs-12">
            <p>* The combination of filters you have selected resulted in no matching events. Please adjust the filters or try different search parameters.</p>
        </div>
    </div>
    <script>

        this.summit                   = opts.summit;
        this.events                   = [];
        this.dic_events               = [];
        this.schedule_filters         = opts.schedule_filters;
        this.search_url               = opts.search_url;
        this.schedule_api             = opts.schedule_api;
        this.base_url                 = opts.base_url;
        this.default_event_color      = opts.default_event_color;
        this.current_filter           = null;
        this.show_date                = false;
        this.day_selected             = '';
        var self                      = this;

        this.on('mount', function(){

            $(document).on('click', '#link_google_sync', function() {

                console.log('clicked link_google_sync');
                var selected_checkboxes  = $('.select-event-chk:checkbox:checked');
                if(selected_checkboxes.length == 0){
                    sweetAlert("Oops...", "you must select at least one event!", "error");
                    return true;
                }

                var selected_events = [];
                selected_checkboxes.each(function(){
                    var event_id = $(this).data('event-id');
                    var event    = self.dic_events[event_id];
                    if(event.gcal_id != '' && event.gcal_id != null){
                        sweetAlert("Oops...", "you selected one or more events that are already synched!", "error");
                        return true;
                    }
                    selected_events.push(event);
                });

                if(!GoogleCalendarApi.isAuthorized()){
                    GoogleCalendarApi.doUserAuth(function(){
                        GoogleCalendarApi.addEvents(selected_events, self.addEventCallback);
                        selected_checkboxes.prop( "checked", false );
                    });
                    return true;
                }
                GoogleCalendarApi.addEvents(selected_events, self.addEventCallback);
                selected_checkboxes.prop( "checked", false );
                return true;
            });

             $(document).on('click', '#link_google_unsync', function() {
                console.log('clicked link_google_unsync');
                var selected_checkboxes  = $('.select-event-chk:checkbox:checked');
                if(selected_checkboxes.length == 0){
                    sweetAlert("Oops...", "you must select at least one event!", "error");
                    return true;
                }

                var selected_events = [];
                selected_checkboxes.each(function(){
                    var event_id = $(this).data('event-id');
                    var event    = self.dic_events[event_id];
                    if(event.gcal_id == '' || event.gcal_id == null){
                        sweetAlert("Oops...", "you selected one or more events that are not synched!", "error");
                        return true;
                    }
                    selected_events.push(event);
                });

                if(!GoogleCalendarApi.isAuthorized()){
                    GoogleCalendarApi.doUserAuth(function(){
                        GoogleCalendarApi.removeEvents(selected_events, self.removeEventCallback);
                         selected_checkboxes.prop( "checked", false );
                    });
                    return true;
                }
                GoogleCalendarApi.removeEvents(selected_events, self.removeEventCallback);
                selected_checkboxes.prop( "checked", false );
                return true;
            });

            $(document).on('click', '#link_export_ics', function() {
                console.log('clicked link_export_ics');
                var selected_checkboxes  = $('.select-event-chk:checkbox:checked');
                if(selected_checkboxes.length == 0){
                    sweetAlert("Oops...", "you must select at least one event!", "error");
                    return true;
                }
                var selected_events = [];
                selected_checkboxes.each(function(){
                    var event_id = $(this).data('event-id');
                    selected_events.push(event_id);
                });
                var url = 'api/v1/summits/@SUMMIT_ID/schedule/export/ics';
                url     = url.replace('@SUMMIT_ID', self.summit.id)+'?events_id='+selected_events.join();

                window.open(url,"", "width=0,height=0,menubar=no,location=no,resizable=no,scrollbars=no,status=no");
                return true;
            });

            $(document).off("click",".btn-go-event").on("click",".btn-go-event", function(e){
                 e.preventDefault();
                 e.stopPropagation();
                 var event_url = $(this).attr('href');
                 var url       = new URI(event_url);
                 // add back url
                 $(window).url_fragment('setParam','eventId', $(this).data('event-id'));
                 window.location.hash = $(window).url_fragment('serialize');
                 url.addQuery('BackURL', window.location)
                 window.location = url.toString();
                 return false;
            });

            $(document).off("click", ".event-action-link").on("click", ".event-action-link", function(e) {
                e.preventDefault();
                e.stopPropagation();
                var event_id = $(this).data('event-id');
                if($(this).hasClass('disabled') || $(this).parent().hasClass('disabled')) return false;
                $('#event_actions_'+event_id).removeClass('open');
                var type     = $(this).data('type');
                var event    = self.dic_events[event_id];
                switch(type){
                    case 'going':
                         self.schedule_api.addEvent2MySchedule(self.summit.id, event.id);
                         event.going  = true;
                         self.updateState(event);
                    break;
                    case 'not-going':
                        self.schedule_api.removeEventFromMySchedule(self.summit.id, event.id);
                        event.going  = false;

                        //check if we are on my schedule view
                        if(self.current_filter.going){
                            //fade animation
                            $('#event_'+event_id).fadeOut({ duration: 1000, queue: false }).slideUp(200);
                        }
                        else
                             self.updateState(event);
                    break;
                    case 'watch':
                        self.schedule_api.addEvent2MyFavorites(self.summit.id, event.id);
                        event.favorite  = true;
                        self.updateState(event);
                    break;
                    case 'unwatch':
                       self.schedule_api.removeEventFromMyFavorites(self.summit.id, event.id);
                       event.favorite  = false;
                       if(self.current_filter.favorites){
                            //fade animation
                            $('#event_'+event_id).fadeOut({ duration: 1000, queue: false }).slideUp(200);
                       }
                       else
                            self.updateState(event);
                    break;
                    case 'rsvp':
                        event.going  = true;
                        if(event.rsvp_external){
                            self.schedule_api.addEvent2MySchedule(self.summit.id, event.id);
                            self.updateState(event);
                        }
                        else
                        {
                            // our custom one, just navigate
                            var url = new URI(event.rsvp_link);
                            $(window).url_fragment('setParam','eventId', event_id);
                            window.location.hash = $(window).url_fragment('serialize');
                            url.addQuery('BackURL', window.location);
                            window.location = url.toString();
                        }
                        self.updateState(event);
                    break;
                    case 'unrsvp':
                       event.going  = false;
                       self.schedule_api.unRSVPEvent(self.summit.id, event.id);
                       self.updateState(event);
                    break;
                }

                return false;
            });

            // show event details handler (jquery)
            $(document).off("click", ".main-event-content").on( "click", ".main-event-content", function(e) {

                if($(e.target).is('.icon-event-action, .event-action-link')){
                    return false;
                }

                if($(e.target).is('.synch-container, .select-event-chk')){
                    return true;
                }

                if($(e.target).is('.synch-container, .sync-icon')){
                    return false;
                }

                if($(e.target).hasClass('search-link')) return true;

                var event_id  = $(e.currentTarget).attr('data-event-id');
                var detail    = $('#event_details_'+event_id);
                var must_load = !detail.hasClass('loaded');

                if ( detail.is( ":hidden" ) ) {
                    detail.slideDown( "slow" );
                    if(must_load){
                        detail.html('<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>');
                        var url =  self.parent.base_url+'events/'+ event_id+'/html'
                            $.ajax({
                            type    : 'GET',
                            url     : url,
                            timeout : 60000,
                            cache   : false,
                            success : function (data, textStatus, jqXHR) {
                                detail.html(data);
                                detail.addClass('loaded');
                            }
                        }).fail(function (jqXHR, textStatus, errorThrown) {
                            alert('there was an error, please contact your administrator');
                        });
                    }
                }
                else {
                    detail.slideUp( "slow" );
                }
                e.preventDefault();
                e.stopPropagation();
                return false;
            });

            $(document).off("click",".sync-icon").on("click",".sync-icon", function(e){
                e.preventDefault();
                e.stopPropagation();

                var event_id        = $(e.currentTarget).parents('.main-event-content').attr('data-event-id');
                var event           = self.dic_events[event_id];
                event.location      = self.getSummitLocation(event);
                var selected_events = [event];

                if($(this).hasClass('icon-sync-event')){
                    // check auth
                    if(!GoogleCalendarApi.isAuthorized()){
                            GoogleCalendarApi.doUserAuth(function(){
                                GoogleCalendarApi.removeEvents(selected_events, self.removeEventCallback);
                            });
                            return false;
                    }
                    GoogleCalendarApi.removeEvents(selected_events, self.removeEventCallback);
                }
                if($(this).hasClass('icon-unsync-event')){
                    // check auth
                    if(!GoogleCalendarApi.isAuthorized()){
                        GoogleCalendarApi.doUserAuth(function(){
                            GoogleCalendarApi.addEvents(selected_events, self.addEventCallback);
                        });
                        return false;
                     }
                     GoogleCalendarApi.addEvents(selected_events, self.addEventCallback);
                }
                return false;
            });
        });

        addEventCallback(response, event){
            event.gcal_id = response.result.id;
            console.log(" event.id "+event.id+" cal id "+response.result.id);
            self.schedule_api.googleCalSynch(event);
        }

        removeEventCallback(response, event){
            self.schedule_api.googleCalUnSynch(event);
        }

        this.schedule_api.on('googleEventSynchSaved', function(event){
            var container    = $('.icon-event-synched[data-event-id="'+event.id+'"]');
            var synch_button = $('.sync-icon',container);
            if (synch_button.hasClass('icon-unsync-event')) {
                synch_button.removeClass('icon-unsync-event').addClass('icon-sync-event');
                synch_button.attr('title','Syncronized');
                return;
            }
            event.gcal_id = '';
            synch_button.removeClass('icon-sync-event').addClass('icon-unsync-event');
            synch_button.attr('title','Unsyncronized');
        });

        this.schedule_api.on('beforeEventsRetrieved', function(){
            $('#events-container').ajax_loader();
        });

        this.schedule_api.on('addedEvent2MySchedule', function(event_id){
              var event    = self.dic_events[event_id];
              if(event.has_rsvp && event.rsvp_external){
                  var url      = new URI(event.rsvp_link);
                  url.addQuery('BackURL',window.location)
                  window.location = url.toString();
              }
        });

        this.schedule_api.on('eventsRetrieved',function(data) {
            self.show_date    = data.show_date;
            self.day_selected = data.day_selected;
            self.events       = data.events;

            var actions_container = self.summit.current_user !== null ?
            '<a class="event-actions-menu" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" title="event actions">'+
            '<span class="caret caret-event-actions"></span>'+
            '</a>'+
            '<ul class="dropdown-menu dropdown-menu-event-actions">'+
            '<li class="rsvp-action event-action"><a data-type="rsvp" class="event-action-link" href="#"><i class="fa fa-check-circle" aria-hidden="true"></i>&nbsp;RSVP</a></li>'+
            '<li class="unrsvp-action event-action"><a data-type="unrsvp" class="event-action-link" href="#"><i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;unRSVP</a></li>'+
            '<li class="going-action event-action"><a data-type="going" class="event-action-link" href="#"><i class="fa fa-check-circle" aria-hidden="true"></i>&nbsp;Schedule</a></li>'+
            '<li class="not-going-action event-action"><a data-type="not-going" class="event-action-link" href="#"><i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;UnSchedule</a></li>'+
            '<li class="watch-action event-action"><a data-type="watch" class="event-action-link" href="#"><i class="fa fa-bookmark" aria-hidden="true"></i>&nbsp;Watch Later</a></li>'+
            '<li class="unwatch-action event-action"><a data-type="unwatch" class="event-action-link" href="#"><i class="fa fa-bookmark-o" aria-hidden="true"></i>&nbsp;Do not Watch Later</a></li>'+
            '<li role="separator" class="divider"></li>'+
            '<li class="cancel-action event-action"><a data-type="cancel" class="event-action-link" href="#">Cancel</a></li>'+
            '</ul>'
            : '';

            var cal_synch_container = self.summit.current_user !== null ? '<div style="display:none" class="col-md-2 synch-container">'+
            '<span class="icon-event-synched">'+
            '<i class="fa fa-refresh sync-icon" title="" style="cursor:pointer" aria-hidden="true"></i>&nbsp;Sync&nbsp;'+
            '</span>'+
            '<input type="checkbox" title="select event" class="select-event-chk"/>'+
            '</div>' : '';

            var event_template = $(
            '<div class="col-md-12">'+
                '<div class="row event-row">'+
                '<div class="col-sm-12">'+
                '<div class="row main-event-content row-eq-height">'+
                '<div class="event-content col-sm-10 col-xs-10">'+
                '<div class="row row_location">'+
                '<div class="col-sm-3 col-time">'+
                '<i class="fa fa-clock-o icon-clock">'+
                '</i><span class="event-date"></span>&nbsp;'+
                '<span class="start-time" data-epoch=""></span>-<span class="end-time"></span>'+
                '</div>'+
                '<div class="col-sm-8 col-location">'+
                '<div>'+
                ( (self.summit.should_show_venues)? '<i class="fa fa-map-marker icon-map"></i>&nbsp;<a class="search-link venue-search-link"></a>' : '')+
                '</div>'+
                '</div>'+
                '</div>'+
                '<div class="row">'+
                '<div class="col-md-10">'+
                '<span class="event-title"></span>'+
                '<a class="search-link attachment-link" target="_blank"><i class="search-link fa fa-download" aria-hidden="true"></i></a>'+
                '<span class="record-icon"><i class="fa fa-video-camera" aria-hidden="true"></i></span>'+
                '</div>'+
                cal_synch_container+
                '</div>'+
                '<div class="row">'+
                '<div class="col-xs-8 col-track">'+
                '<span class="track">'+
                '<a class="search-link track-search-link" title="Search Track"></a>'+
                '</span>'+
                '</div>'+
                '<div class="col-xs-4 event-type-col">'+
                '<a class="search-link event-type-search-link" title="Search Event Type"></a></div>'+
                '</div>'+
                '</div>'+
                '<div class="event-state col-sm-1 col-xs-1">'+
                '<i class="fa fa-check-circle going-status event-status" aria-hidden="true"></i>'+
                '<i class="fa fa-bookmark favorite-status event-status" aria-hidden="true"></i>'+
                '</div>'+
                '<div class="event-actions-container col-sm-1 col-xs-1">'+  actions_container +'</div>'+
                '</div>'+
                '<div class="row event-details" style="display:none;"></div>'+
                '</div>'+
                '</div>'+
            '</div>');

            console.log(self.events.length +' events retrieved ...');

            var event_directives  = {
                'div.event-details@id':  function (arg){ return 'event_details_'+arg.item.id; },
                '@id': function(arg){ return 'event_'+arg.item.id; },
                'span.event-title': 'event.title',
                'a.attachment-link@style': function(arg){
                    return (arg.item.attachment_url != '') ? '' : 'display:none';
                },
                'a.attachment-link@href': function(arg){
                    return arg.item.attachment_url;
                },
                'span.record-icon@style': function(arg){
                    return (arg.item.to_record) ? '' : 'display:none';
                },
                'div.main-event-content@data-event-id': 'event.id',
                'div.main-event-content@style': function (arg){
                    var category_group_ids = arg.item.category_group_ids;
                    var type_id = arg.item.type_id;
                    var color = (category_group_ids.length == 0)? self.summit.event_types[type_id].color :
                    self.summit.category_groups[category_group_ids[0]].color;
                    return 'border-left: 3px solid '+color;
                },
                'span.track@style': function(arg){ arg.item.class_name === 'SummitEvent' ? 'display:none' : '';},
                'a.track-search-link': function(arg){
                    var track_id = arg.item.track_id;
                    if(typeof track_id !== "undefined" && track_id > 0){
                        return self.summit.tracks[track_id].name;
                    }
                    return '';
                },
                'a.track-search-link@href': function(arg){
                    var track_id   = arg.item.track_id;
                    var track_name = '';
                    if(typeof track_id !== "undefined" && track_id > 0){
                        track_name = self.summit.tracks[track_id].name;
                    }
                    return track_name != '' ? self.search_url+'?t='+encodeURIComponent(track_name.replace(/ /g,'+')) : '';
                },
                'a.event-type-search-link': function(arg){ return self.summit.event_types[arg.item.type_id].type; },
                'a.event-type-search-link@href': function(arg){ return self.search_url+'?t='+encodeURIComponent(self.summit.event_types[arg.item.type_id].type.replace(/ /g,'+'))  },
            };

            if(self.show_date){
                event_directives['span.event-date'] =  function(arg){ return ' '+arg.item.date_nice+' ,';};
            }

            event_directives['span.start-time'] =  'event.start_time';
            event_directives['span.start-time@data-epoch'] = 'event.start_epoch',
            event_directives['span.end-time']   =  'event.end_time';

            if(self.summit.should_show_venues){
                 event_directives['a.venue-search-link'] = function(arg){ return self.getSummitLocation(arg.item);};
                 event_directives['a.venue-search-link@href'] = function(arg){
                    if (arg.item.location_id) {
                        return self.summit.locations[arg.item.location_id].link;
                    } else {
                        return self.summit.link + 'venues';
                    }
                 };
            }

            if(self.summit.current_user !== null ){
                // EVENT ACTIONS
                event_directives['div.event-actions-container@id'] = function(arg){
                   var item = arg.item;
                   self.dic_events[item.id] = item;
                   return 'event_actions_'+item.id;
                };
                 event_directives['div.event-actions-container@data-event-id'] = function(arg){
                                   return arg.item.id;
                                };
                event_directives['a.event-actions-menu@id'] = function(arg){ return 'event_action_menu_'+arg.item.id; }
                event_directives['ul.dropdown-menu@aria-labelledby'] = function(arg){ return 'event_action_menu_'+arg.item.id; }
                event_directives['a.event-action-link@data-event-id']     = function(arg){ return arg.item.id; };
                event_directives['.event-state@id']                       = function(arg){ return 'event_state_'+arg.item.id; };
                // GOOGLE CALENDAR SYNC
                event_directives['i.sync-icon@title']                     = function(arg){ return arg.item.gcal_id != ''  && arg.item.gcal_id != null ? 'Syncronized':'Unsyncronized'; };
                event_directives['i.sync-icon@class+']                    = function(arg){ return arg.item.gcal_id != ''  && arg.item.gcal_id != null ? ' icon-sync-event':' icon-unsync-event'; };
                event_directives['span.icon-event-synched@data-event-id'] = function(arg){ return arg.item.id; };
                event_directives['input.select-event-chk@id']             = function(arg){ return 'select_event_chk_'+ arg.item.id; };
                event_directives['input.select-event-chk@data-event-id']  = function(arg){ return arg.item.id; };
            }
            else
            {
                event_directives['div.event-state@class+'] = function(arg){ return ' hide'};
                event_directives['div.event-actions-container@class+'] = function(arg){  return ' hide'};
            }

            var directives = {
                'div.event-row':{
                    'event<-': event_directives
                }
            };

            var html = event_template.render(data.events, directives);
            $('#events-inner-container').html(html);
            self.scrollToTime();
            self.applyFilters();

            $('.event-actions-container').on('show.bs.dropdown', function (e) {
               var event_id = $(this).data('event-id');
               var event    = self.dic_events[event_id];
               self.updateMenu(event);
            });

            window.setTimeout(function(){
                var eventId   = $(window).url_fragment('getParam','eventid');
                if(eventId != null){
                 if($('#event_'+eventId).length > 0) {
                        $('html, body').animate({
                            scrollTop: $('#event_'+eventId).offset().top
                        }, 1000);
                    }
                }
                $('#events-container').ajax_loader('stop');
            }, 1000);
        });

        this.schedule_filters.on('scheduleFiltersChanged', function(filters){
            self.current_filter = filters;
            self.applyFilters();
        });

        updateMenu(event){
            var menu = $('#event_actions_'+event.id);
            $('li.rsvp-action', menu).hide();
            $('li.unrsvp-action', menu).hide();
            $('li.not-going-action', menu).hide();
            $('li.going-action', menu).hide();
            $('li.watch-action', menu).hide();
            $('li.unwatch-action', menu).hide();

            if(!self.current_filter.favorites && self.summit.current_user.is_attendee){
                if(!event.has_rsvp){
                    if( event.going ){
                        $('li.not-going-action', menu).show();
                    }
                    else{
                        $('li.going-action', menu).show();
                    }
                }
                else{
                    // RSVP
                    if( ! event.going ){
                        $('li.rsvp-action', menu).show();
                        if(event.rsvp_seat_type == 'FULL')
                            $('li.rsvp-action', menu).addClass('disabled');
                        else
                            $('li.rsvp-action', menu).removeClass('disabled');
                    }
                    else{
                         if(!event.rsvp_external)
                            $('li.unrsvp-action', menu).show();
                    }
                }
            }

            if(event.favorite){
                $('li.unwatch-action', menu).show();
            }
            else{
                $('li.watch-action', menu).show();
            }

        }

        updateState(event){
            var state = $('#event_state_' + event.id);
            $('i.favorite-status', state).css('display','none');
            $('i.going-status', state).css('display','none');
            if(self.summit.current_user && self.summit.current_user.is_attendee){
                if(event.going)
                    $('i.going-status', state).css('display','');
            }
            if(!event.going && event.favorite){
                $('i.favorite-status', state).css('display','');
            }
        }

        showOnlyFavoriteState(event){
             var state = $('#event_state_' + event.id);
             if(event.favorite){
                $('i.favorite-status', state).css('display','');
                $('i.going-status', state).css('display','none');
             }
        }

        showOnlyGoingState(event){
            var state = $('#event_state_' + event.id);
            if(event.going){
                $('i.favorite-status', state).css('display','none');
                $('i.going-status', state).css('display','');
            }
        }

        isFilterEmpty() {
            return self.isTrackGroupsFilterEmpty() && self.isEventTypesFilterEmpty() && self.isTracksFilterEmpty() && self.isLevelsFilterEmpty() && self.isTagsFilterEmpty() && self.isMyScheduleFilterEmpty() && self.isMyFavoritesFilterEmpty();
        }

        isEventTypesFilterEmpty() {
            return (self.current_filter.event_types === null || self.current_filter.event_types.length === 0);
        }

        isTrackGroupsFilterEmpty() {
            return (self.current_filter.track_groups === null || self.current_filter.track_groups.length === 0);
        }

        isTracksFilterEmpty() {
            return (self.current_filter.tracks === null || self.current_filter.tracks.length === 0);
        }

        isLevelsFilterEmpty() {
            return (self.current_filter.levels === null || self.current_filter.levels.length === 0);
        }

        isTagsFilterEmpty() {
            return (self.current_filter.tags === null || self.current_filter.tags.length === 0);
        }

        isMyScheduleFilterEmpty() {
            return (!self.current_filter.going);
        }

        isMyFavoritesFilterEmpty() {
            return (!self.current_filter.favorites);
        }

        getSummitLocation(event) {
            var location = self.summit.locations[event.location_id];
            if (typeof location == 'undefined') return 'TBA';
            else return location.name_nice;
        }

        applyFilters(){
            var event_count = 0;

            $('.event-row').show();
            // show select checkbox only if my schedule
            if(self.current_filter.own)
                $('.synch-container').show();
            else
                $('.synch-container').hide();

                for(var e of self.events){
                    var show = true;
                    self.updateState(e);
                    //track groups
                    if(!self.isTrackGroupsFilterEmpty())
                        show &= e.hasOwnProperty('track_id') ? self.current_filter.track_groups.some(function(v) { return self.summit.category_groups[parseInt(v)].tracks.indexOf(parseInt(e.track_id)) != -1; }) : false;
                    if(!show){ $('#event_'+e.id).hide(); continue;}
                    //eventypes
                    if(!self.isEventTypesFilterEmpty())
                        show &= self.current_filter.event_types.indexOf(e.type_id.toString()) > -1;
                    if(!show){ $('#event_'+e.id).hide(); continue;}
                    //tracks
                    if(!self.isTracksFilterEmpty())
                        show &=  e.hasOwnProperty('track_id') ? self.current_filter.tracks.indexOf(e.track_id.toString()) > -1 : false;
                    if(!show){ $('#event_'+e.id).hide(); continue;}
                    //level
                    if(!self.isLevelsFilterEmpty())
                        show &= e.hasOwnProperty('level') ? self.current_filter.levels.indexOf(e.level.toString()) > -1 : false;
                    if(!show){ $('#event_'+e.id).hide(); continue;}
                    //tags
                    if(!self.isTagsFilterEmpty())
                        show &= e.tags_id.some(function(v) { return self.current_filter.tags.indexOf(v.toString()) != -1; });
                    if(!show){ $('#event_'+e.id).hide(); continue;}
                    //my schedule
                    if(self.current_filter.going){
                        show &= e.going;
                        if(e.going) self.showOnlyGoingState(e);
                    }
                    if(!show){ $('#event_'+e.id).hide(); continue;}

                    //my favorites
                    if(self.current_filter.favorites){
                        // favorites view
                        show &= e.favorite;
                        if(e.favorite) self.showOnlyFavoriteState(e);
                    }
                    if(!show){ $('#event_'+e.id).hide(); continue;}

                    $('#event_'+e.id).show();
                    event_count++;
                }

                if(event_count == 0) {
                    $('#no_events_msg').show();
                } else {
                    $('#no_events_msg').hide();
                }
        }

        scrollToTime() {
            var d = new Date();
            var month = d.getMonth()+1;
            var day = d.getDate();
            var date = d.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day;

            if (date == self.day_selected) {
                var current_event = $('.start-time[data-epoch]').filter(function () {
                    return $(this).data('epoch') >= ($.now()/1000);
                }).first();

                if (current_event) {
                    $('html, body').animate({
                        scrollTop: current_event.parents('.event-row').offset().top
                    }, 2000);
                }
            }
        }

     </script>
</schedule-grid-events>