<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<schedule-grid>
    <div class="row">
        <nav class="navbar navbar-default navbar-days">
            <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand navbar-brand-month" href="{ base_url }">{ month }</a>
                </div>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li class="{ active: parent.selected_day.date == date, day-selected: parent.selected_day.date == date }" each={ summit.dates } ><a href="#" class="day-label" onclick={ parent.selectDate }>{ label }</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    <div class="row">
            <div class="container" id="events-container">
               <schedule-main-filters summit="{ summit }"></schedule-main-filters>
                <div class="row">
                    <div class="col-md-12">
                        <div id="event_{ id }" class="row event-row" each={ summit.events[this.selected_day.date] }>
                            <div class="col-md-12">
                                <div class="row main-event-content" data-event-id="{ id }">
                                    <div class="col-xs-1 event-type" style="background-color: { parent.summit.event_types[type_id].color }">&nbsp;</div>
                                    <div class="col-xs-11 event-content">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <i class="fa fa-clock-o icon-clock"></i>&nbsp;<span>{ start_time }</span>&nbsp;/&nbsp;<span>{ end_time }</span>
                                            </div>
                                            <div class="col-md-1 col-md-offset-12 my-schedule-container" if={ parent.summit.current_user !== null } >
                                                <i if={ !own } class="fa fa-plus-circle icon-foreign-event icon-event-action" title="add to my schedule" onclick={ parent.addToMySchedule } ></i>
                                                <i if={ own } class="fa fa-check-circle icon-own-event icon-event-action" title="remove from my schedule" onclick={ parent.removeFromMySchedule } ></i>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 event-title">
                                                <raw content="{ title }"/>
                                            </div>
                                        </div>
                                        <div class="row"><div class="col-md-12">&nbsp;</div></div>
                                        <div class="row">
                                            <div class="col-md-12"><i class="fa fa-map-marker icon-map"></i>&nbsp;<span>{ locationName(location_id) }</span></div>
                                        </div>
                                        <div class="row"><div class="col-md-4 col-md-offset-9 event-type-col">{ summit.event_types[type_id].type }</div></div>
                                    </div>
                                </div>
                                <div class="row event-details" id="event_details_{ id }" style="display:none;">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-9"><raw content="{ description }"/></div>
                                            <div class="col-md-3">
                                                <div data-speaker-id={ speaker_id } class="row speaker-row" each={ speaker_id in speakers_id }>
                                                    <div class="col-md-4">
                                                        <img src="{ summit.speakers[speaker_id].profile_pic }" class="img-circle" alt="{ summit.speakers[speaker_id].name }">
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="row speaker-name-row"><div class="col-md-12">{ summit.speakers[speaker_id].name }</div></div>
                                                        <div class="row speaker-position-row"><div class="col-md-12">{ summit.speakers[speaker_id].position }</div></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <a href="{ parent.base_url+'event/'+ this.id } " class="btn btn-primary btn-md active btn-warning" role="button">GO TO EVENT</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <script>

        this.summit            = opts.summit;
        this.month             = opts.month;
        this.selected_day      = summit.dates[0];
        this.aux_selected_day  = null;
        this.clicked_event     = {};
        this.api               = opts.schedule_api;
        this.base_url          = opts.base_url;
        var self               = this;

        this.on('mount', function(){

            // show event details handler (jquery)
            $( "body" ).on( "click", ".main-event-content", function(e) {

                if($(e.target).hasClass('icon-event-action')){
                    return false;
                }
                var event_id = $(e.currentTarget).attr('data-event-id');
                var detail   = $('#event_details_'+event_id);

                if ( detail.is( ":hidden" ) ) {
                    detail.slideDown( "slow" );
                } else {
                    detail.slideUp( "slow" );
                }
            });
        });

        selectDate(e) {
            var day = e.item;
            console.log('selected date '+ day.date);
            if(self.summit.events[day.date].length === 0) {
                $('#events-container').loading();
                self.api.getEventByDay(self.summit.id, day.date);
                self.aux_selected_day = day;
            }
            else {
                self.selected_day = day;
            }
        }

        opts.schedule_api.on('eventsRetrieved',function(data) {
            console.log('eventsRetrieved');
            self.summit.events[data.day] = data.events;
            self.selected_day            = self.aux_selected_day;
            self.aux_selected_day        = null;
            $('#events-container').loading('stop');
            self.update();
        });

        opts.schedule_api.on('eventAdded2MySchedule',function(event_id) {
            console.log('eventAdded2MySchedule');
            self.clicked_event[event_id].own = true;
            self.update();
            delete self.clicked_event[event_id];
        });

        opts.schedule_api.on('eventRemovedFromMySchedule',function(data) {
            console.log('eventRemovedFromMySchedule');
            self.clicked_event[event_id].own = false;
            self.update();
            delete self.clicked_event[event_id];
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

        addToMySchedule(e) {
            console.log('addToMySchedule');
            self.clicked_event[e.item.id] = e.item;
            self.api.addEvent2MySchedule(self.summit.id, e.item.id);
        }

        removeFromMySchedule(e) {
            console.log('removeFromMySchedule');
            self.clicked_event[e.item.id] = e.item;
            self.api.removeEventFromMySchedule(self.summit.id, e.item.id);
        }
    </script>

</schedule-grid>