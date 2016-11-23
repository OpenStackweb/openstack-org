<raw>
    this.root.innerHTML = opts.content
</raw>

<schedule-my-schedule>
    <div class="panel panel-default" each="{ key, day in events }">
        <div class="panel-heading">{ key }</div>

        <table class="table">
            <thead>
                <tr>
                    <th>&nbsp</th>
                    <th>Time</th>
                    <th>Event</th>
                    <th>Room</th>
                    <th>RSVP</th>
                </tr>
            </thead>
            <tbody>
                <tr each={ event in day } data-id="{ event.id }">
                    <td style="width: 5%;">
                        <input class="select-event-chk" type="checkbox" data-event-id="{ event.id }" />
                        <i data-event-id="{ event.id }" style="cursor:pointer" class="fa fa-refresh sync-icon { icon-sync-event: event.gcal_id != '' && event.gcal_id != null, icon-unsync-event: event.gcal_id == '' || event.gcal_id == null }" title="{  event.gcal_id != '' && event.gcal_id != null ? 'Syncronized' : 'Unsyncronized' }" aria-hidden="true"></i>
                    </td>
                    <td style="width: 15%;">{ event.start_time } - { event.end_time }</td>
                    <td style="width: 40%;">
                        <a href="{ base_url+'events/'+ event.id }" target="_blank">{ event.title }</a>
                        <div class="event_description" style="display:none">
                            <raw content="{ event.description }"/>
                        </div>
                    </td>
                    <td style="width: 20%;" if={ should_show_venues == 1 }>{ event.room }</td>
                    <td style="width: 20%;" if={ should_show_venues == 0 }>TBD</td>
                    <td style="width: 10%;">
                        <a href="{ event.rsvp }" if={ event.rsvp != ''}>RSVP</a>
                        <span if={ event.rsvp == '' }> - </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        this.events             = opts.events;
        this.summit             = opts.summit;
        this.schedule_api       = opts.schedule_api;
        this.dic_events         = opts.dic_events;
        this.should_show_venues = opts.should_show_venues
        this.base_url           = opts.base_url;
        var self                = this;

        this.on('mount', function() {

            $('#show_desc').change(function(){
                $(this).toggleClass('active');
                $('.event_description').toggle();
            });

            // check/uncheck all
            $(document).on('click', '#chk_select_all' , function(){
                var visible_checkboxes = $(".select-event-chk:visible");
                console.log('checked '+visible_checkboxes.length+' events');
                visible_checkboxes.prop('checked', $(this).is(':checked'));
            });

            // google sync
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

            // google unsync
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

            // export to ICS
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

            // sync/unsync individual event
            $(document).on("click",".sync-icon", function(e){
                console.log('clicked sync individual icon ... ');
                e.preventDefault();
                e.stopPropagation();

                var event_id    = $(this).attr('data-event-id');
                console.log('event id '+ event_id);
                var event       = self.dic_events[event_id];
                selected_events = [event];

                if($(this).hasClass('icon-sync-event')){
                    console.log('unsync individual');
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
                    console.log('sync individual');
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

            addEventCallback(response, event){
                event.gcal_id = response.result.id;
                console.log(" event.id "+event.id+" cal id "+response.result.id);
                self.schedule_api.googleCalSynch(event);
            }

            removeEventCallback(response, event){
                self.schedule_api.googleCalUnSynch(event);
            }

            this.schedule_api.on('googleEventSynchSaved', function(event){
                var synch_button    = $('.sync-icon[data-event-id="'+event.id+'"]');
                if (synch_button.hasClass('icon-unsync-event')) {
                    synch_button.removeClass('icon-unsync-event').addClass('icon-sync-event');
                    synch_button.attr('title','Syncronized');
                    return;
                }
                event.gcal_id = '';
                synch_button.removeClass('icon-sync-event').addClass('icon-unsync-event');
                synch_button.attr('title','Unsyncronized');
            });

        });


    </script>

</schedule-my-schedule>