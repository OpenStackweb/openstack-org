<schedule-admin-view>

            <div class="row">
                <div class="col-md-12">
                    <table id="day_schedule">
                    <tbody>
                        <tr>
                            <td class="times-col">
                                <div each={ time_slots } class="time-slot" id='time_slot_{ format("HH_mm") }'>{ format("hh:mm A") }</div>
                            </td>
                            <td class="events-col">
                                <div each={ time_slots } class="time-slot-container" data-time="{ format('HH:mm') }" id="time_slot_container_{ format('HH_mm') }"></div>
                                <schedule-admin-view-schedule-event each="{ schedule_events }" data="{ this }"></schedule-admin-view-schedule-event>
                            </td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>

    <script>

        this.day             = null;
        this.location        = null;
        this.start_time      = moment(opts.start_time, 'HH:mm');
        this.end_time        = moment(opts.end_time, 'HH:mm');
        this.interval        = parseInt(opts.interval);
        this.schedule_events = [];
        this.time_slots      = [];
        this.api             = opts.api;

        var self        = this;
        var done = false;

        var slot = this.start_time ;
        do
        {
            this.time_slots.push(slot);
            slot = slot.clone();
            slot.add('m', this.interval);
            done = slot.isAfter(this.end_time);
        }while(!done);

        this.on('mount', function(){
            $(function() {

                $(".event").draggable({
                    revert: "invalid",
                    containment: "document",
                    cursor: "move"
                });

                $( ".time-slot-container" ).droppable({
                    hoverClass: "ui-state-hover",
                    accept: function(e, ui){return true;},
                    drop: function( e, ui ) {

                        var event  = $(ui.draggable);
                        var id     = event.attr('id');
                        var target = $(this);

                        var top    = target.position().top;
                        var left   = target.position().left;

                        event.addClass('event-published');
                        var start_time = target.attr('data-time');
                        var minutes    = (parseInt(event.css('height'))/3);
                        var end_time   = moment(start_time, 'HH:mm').add('m', minutes);
                        start_time     = moment(start_time, 'HH:mm');

                        console.log('star time '+ start_time.format('HH:mm'));
                        console.log('end time ' + end_time.format('HH:mm'));

                        event.appendTo($('.events-col'));
                        event.css('position','absolute');
                        event.css('top', top + 1);
                        event.css('left', left);

                        self.schedule_events[id] =
                        {
                            start_time : start_time,
                            end_time   : end_time
                        };

                        if( typeof event.resizable( "instance" ) == 'undefined'){
                            event.resizable({
                                containment: ".events-col",
                                maxWidth: 300,
                                minWidth: 300,
                                minHeight: 45,
                                maxHeight: null,
                                grid: 3,
                                handles: {
                                    n: ".ui-resizable-n",
                                    s: ".ui-resizable-s"
                                },
                                animate: true,
                                helper: "ui-resizable-helper",
                                resize: function(e, ui) {

                                },
                                stop: function( event, ui ) {

                                }
                            });
                        }
                    }
                });
            });
        });

        this.api.on('ScheduleByDayAndLocationRetrieved',function(data) {
            console.log('ScheduleByDayAndLocationRetrieved');
            self.schedule_events = data.events;
            $(".event-published").resizable("destroy");
            $(".event-published").draggable("destroy");
            $(".event-published").remove();
            self.update();

            $(".event-published").draggable({
                revert: "invalid",
                containment: "document",
                cursor: "move"
            });

            $(".event-published").resizable({
                containment: ".events-col",
                maxWidth: 300,
                minWidth: 300,
                minHeight: 45,
                maxHeight: null,
                grid: 3,
                handles: {
                    n: ".ui-resizable-n",
                    s: ".ui-resizable-s"
                },
                animate: true,
                helper: "ui-resizable-helper",
                resize: function(e, ui) {

                },
                stop: function( event, ui ) {

                }
            });
        });

    </script>
</schedule-admin-view>