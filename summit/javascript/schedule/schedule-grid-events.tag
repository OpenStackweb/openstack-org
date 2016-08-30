<schedule-grid-events>
    <div class="row" id="events-inner-container">
    </div>
    <div class="row" style="display:none;">
        <div class="col-md-12">
            <p>* There are not events that match your search criteria. </p>
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
        var self                      = this;

        this.on('mount', function(){
            $(document).off("click", ".main-event-content").on( "click", ".icon-event-action", function(e) {
                var event_id = $(this).data('event-id');
                var event    = self.dic_events[event_id];
                if($(this).hasClass('foreign')){
                    // add to my schedule
                    self.schedule_api.addEvent2MySchedule(self.summit.id, event.id);
                    $(this).removeClass('foreign');
                    event.own  = true;
                    $('.myschedule-icon',$(this)).removeClass('icon-foreign-event');
                    $('.myschedule-icon',$(this)).addClass('icon-own-event');
                    $(this).addClass('own');
                    return false;
                }
                if($(this).hasClass('own')){
                    // add to my schedule
                    self.schedule_api.removeEventFromMySchedule(self.summit.id, event.id);
                    $(this).removeClass('own');
                    event.own  = false;
                    $('.myschedule-icon',$(this)).removeClass('icon-own-event');
                    $('.myschedule-icon',$(this)).addClass('icon-foreign-event');
                    $(this).addClass('foreign');
                    return false;
                }
            });
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
                        detail.html('<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>');
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

            $(document).off("click",".gcal-synch").on("click",".gcal-synch", function(e){
                e.preventDefault();
                e.stopPropagation();

                var event_id = $(e.currentTarget).parents('.main-event-content').attr('data-event-id');
                var event    = self.dic_events[event_id];
                event.location = self.getSummitLocation(event);

                if($(this).hasClass('foreign')){
                    // synch with google cal
                    self.schedule_api.googleCalSynch(self.summit.id, event);
                    return false;
                }
                if($(this).hasClass('own')){
                    // unsynch with google
                    self.schedule_api.googleCalUnSynch(self.summit.id, event);
                    return false;
                }
            });

        });

        this.schedule_api.on('beforeEventsRetrieved', function(){
            $('#events-container').ajax_loader();
        });

        this.schedule_api.on('eventsRetrieved',function(data) {
            self.show_date    = data.show_date;
            self.events       = data.events;

            var myschedule_container = self.summit.current_user !== null ? '<div class="col-sm-3 my-schedule-container">'+
            '<span class="icon-event-action">'+
            '<i class="fa fa-plus-circle myschedule-icon"></i>&nbsp;My&nbsp;schedule</span>'+
            '</div>' : '';

            var cal_synch_container = self.summit.current_user !== null ? '<div class="col-md-2 gcal-synch-container">'+
            '<div class="btn-group">'+
            '<button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'+
            'Synch to Cal'+
            '</button>'+
            '<div class="dropdown-menu">'+
            '<a class="dropdown-item gcal-synch" href="#"><i class="fa fa-check-circle gcal-icon"></i>&nbsp;Google&nbsp;synch</a><br>'+
            '<a class="dropdown-item export_event search-link" href="">Export&nbsp;ICS</a>'+
            '</div>'+
            '</div>'+
            '</div>' : '';

            var event_template = $(
            '<div class="col-md-12">'+
                '<div class="row event-row">'+
                '<div class="col-sm-12">'+
                '<div class="row main-event-content">'+
                '<div class="event-content">'+
                '<div class="row row_location">'+
                '<div class="col-sm-3 col-time">'+
                '<i class="fa fa-clock-o icon-clock"></i>&nbsp;<span class="start-time"></span>-<span class="end-time"></span></div>'+
                '<div class="col-sm-6 col-location">'+
                '<div>'+
                ( (self.summit.should_show_venues)? '<i class="fa fa-map-marker icon-map"></i>&nbsp;<a class="search-link venue-search-link"></a>' : '')+
                '</div>'+
                '</div>'+
                myschedule_container+
                '</div>'+
                '<div class="row">'+
                '<div class="col-md-10 event-title"></div>'+
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
                '</div>'+
                '<div class="row event-details" style="display:none;"></div>'+
                '</div>'+
                '</div>'+
            '</div>');

            console.log(self.events.length +' events retrieved ...');

            var event_directives  = {
                'div.event-details@id':  function (arg){ return 'event_details_'+arg.item.id; },
                '@id': function(arg){ return 'event_'+arg.item.id; },
                'div.event-title': 'event.title',
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
                    if(typeof track_id !== "undefined"){
                        return self.summit.tracks[track_id].name;
                    }
                    return '';
                },
                'a.track-search-link@href': function(arg){
                    var track_id = arg.item.track_id;
                    var track_name = '';
                    if(typeof track_id !== "undefined"){
                        track_name = self.summit.tracks[track_id].name;
                    }
                    return track_name != '' ? self.search_url+'?t='+track_name.replace(/ /g,'+') : '';
                },
                'a.event-type-search-link': function(arg){ return self.summit.event_types[arg.item.type_id].type; },
                'a.event-type-search-link@href': function(arg){ return self.search_url+'?t='+self.summit.event_types[arg.item.type_id].type.replace(/ /g,'+')  },


                'span.start-time': 'event.start_time',
                'span.end-time': 'event.end_time',
            };

            if(self.summit.should_show_venues){
                 event_directives['a.venue-search-link'] = function(arg){ return self.getSummitLocation(arg.item);};
                 event_directives['a.venue-search-link@href'] = function(arg){ return self.summit.locations[arg.item.location_id].link;};
            }

            if(self.summit.current_user !== null ){
                // MY SCHEDULE
                event_directives['i.myschedule-icon@class+']             = function(arg){ return arg.item.own ? ' icon-own-event':' icon-foreign-event'; };
                event_directives['span.icon-event-action@id']            = function(arg){ return 'event_myschedule_action_'+arg.item.id};
                event_directives['span.icon-event-action@title']         = function(arg){ return arg.item.own ? 'remove from my schedule':'add to my schedule'; };
                event_directives['span.icon-event-action@data-event-id'] = function(arg){
                    var item = arg.item;
                    self.dic_events[item.id] = item;
                    return item.id;
                };
                // GOOGLE CALENDAR SYNCH
                event_directives['i.gcal-icon@class+']              = function(arg){ return arg.item.gcal_id ? ' icon-own-event':' icon-foreign-event'; };
                event_directives['a.gcal-synch@title']              = function(arg){ return arg.item.gcal_id ? 'unsynch from google calendar':'synch with google calendar'; };
                event_directives['a.gcal-synch@data-event-id']      = function(arg){ return arg.item.id; };

                event_directives['a.gcal-synch@class+']             = function(arg){ return arg.item.gcal_id ? ' own':' foreign'; };
                event_directives['span.icon-event-action@class+']   = function(arg){ return arg.item.own ? ' own':' foreign'; };
                event_directives['a.export_event@href']             = function(arg){
                                                                        var event_id = +arg.item.id;
                                                                        return self.parent.base_url+'events/'+event_id+'/export_ics';
                                                                      };
            }

            var directives = {
                'div.event-row':{
                    'event<-': event_directives
                }
            };

            var html = event_template.render(data.events, directives);
            $('#events-inner-container').html(html);
            self.applyFilters();
            window.setTimeout(function(){$('#events-container').ajax_loader('stop');}, 1000);
        });

        this.schedule_filters.on('scheduleFiltersChanged', function(filters){
            self.current_filter = filters;
            self.applyFilters();
        });

        this.schedule_api.on('googleEventSynchSaved', function(event_id, cal_event_id){
            self.dic_events[event_id].gcal_id = cal_event_id;
            var synch_button = $('.gcal-synch[data-event-id="'+event_id+'"]');
            if (synch_button.hasClass('foreign')) {
                synch_button.removeClass('foreign').addClass('own');
                $('.gcal-icon',synch_button).removeClass('icon-foreign-event').addClass('icon-own-event');
            } else {
                synch_button.removeClass('own').addClass('foreign');
                $('.gcal-icon',synch_button).removeClass('icon-own-event').addClass('icon-foreign-event');
            }
        });

        isFilterEmpty() {
            return self.isTrackGroupsFilterEmpty() && self.isEventTypesFilterEmpty() && self.isTracksFilterEmpty() && self.isLevelsFilterEmpty() && self.isTagsFilterEmpty() && self.isMyScheduleFilterEmpty();
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
            return (!self.current_filter.own);
        }

        getSummitLocation(event) {
            var location = self.summit.locations[event.location_id];
            if (typeof location == 'undefined') return 'TBA';
            else return location.name_nice;
        }

        applyFilters(){
            $('.event-row').show();
            if(!self.isFilterEmpty()){
                    for(var e of self.events){
                        var show = true;
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
                        if(self.current_filter.own)
                            show &= e.own;
                        if(!show){ $('#event_'+e.id).hide(); continue;}
                        $('#event_'+e.id).show();
                    }
            }
        }

        serializeObject(form) {
            var o = {};
            var a = form.serializeArray();
            $.each(a, function() {
                if (o[this.name] !== undefined) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]];
                    }
                    o[this.name].push(this.value || '');
                } else {
                    o[this.name] = this.value || '';
                }
            });
            return o;
        }

     </script>
</schedule-grid-events>