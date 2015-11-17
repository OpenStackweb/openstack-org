<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<schedule-grid>
    <div class="row days-nav">
            <div class="col-xs-1 month-container">
                <span>{ month }</span>
            </div>
            <div class="col-xs-1 day-container { day-selected: parent.selected_day.date == date}" each={ summit.dates }>
                <a href="#" class="day-label { day-selected: parent.selected_day.date == date}" onclick={ parent.selectDate } >{ label }</a>
            </div>
    </div>
    <div class="row">
            <div class="container" id="events-container">
                <div class="row">
                    <div class="col-md-12">
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
                                                <div class="col-md-1 col-md-offset-12 my-schedule-container">
                                                    <i if={ !own } class="fa fa-plus-circle icon-foreign-event" title="add to my schedule" onclick={ parent.addToMyScheduSummle} ></i>
                                                    <i if={ own } class="fa fa-check-circle icon-own-event" title="remove from my schedule" onclick={ parent.removeFromMySchedule} ></i>
                                                </div>
                                                <!--
                                                <div class="col-md-2">
                                                    <i class="fa fa-star-o icon-non-favorite-event"></i>
                                                    <i class="fa fa-star icon-favorite-event"></i>
                                                </div>
                                                -->
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
                                                <div class="col-md-9">
                                                    <raw content="{ description }"/>
                                                </div>
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
            </div>
    </div>
    <script>

        this.summit            = opts.summit;
        this.month             = opts.month;
        this.selected_day      = summit.dates[0];
        this.aux_selected_day  = null;
        this.api               = opts.schedule_api;
        this.base_url          = opts.base_url;
        var self               = this;

        this.on('mount', function(){

            // show event details handler (jquery)
            $( "body" ).on( "click", ".main-event-content", function(e) {
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
        }

        removeFromMySchedule(e) {
            console.log('removeFromMySchedule');
        }
    </script>

</schedule-grid>