<schedule-admin-view-published>

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
                                <schedule-admin-view-schedule-event each="{ key, e in schedule_events }" data="{ e }" minute_pixels="{ parent.minute_pixels }" interval="{ parent.interval }"></schedule-admin-view-schedule-event>
                            </td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>

    <script>

        this.day               = null;
        this.location          = null;
        this.start_time        = moment(opts.start_time, 'HH:mm');
        this.end_time          = moment(opts.end_time, 'HH:mm');
        this.interval          = parseInt(opts.interval);
        this.minute_pixels     = parseInt(opts.minute_pixels);
        this.slot_width        = parseInt(opts.slot_width);
        this.dispatcher        = opts.dispatcher;
        this.unpublished_store = opts.unpublished_store;
        this.schedule_events   = {};
        this.time_slots        = [];
        this.api               = opts.api;
        var self               = this;
        var done               = false;

        // create UI
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

                self.createDraggable($(".event"));

                $( ".time-slot-container" ).droppable({
                    hoverClass: "ui-state-hover",
                    drop: function( e, ui ) {

                        var event  = $(ui.draggable);
                        var id     = parseInt(event.attr('data-id'));
                        var target = $(this);

                        var top    = target.position().top;
                        var left   = target.position().left;

                        event.addClass('event-published');
                        var start_time = target.attr('data-time');
                        var minutes    = (parseInt(event.css('height'))/ self.minute_pixels);
                        var end_time   = moment(start_time, 'HH:mm').add('m', minutes);
                        start_time     = moment(start_time, 'HH:mm');

                        console.log('star time '+ start_time.format('HH:mm'));
                        console.log('end time ' + end_time.format('HH:mm'));

                        for(var id2 in self.schedule_events)
                        {
                            if(parseInt(id2) === parseInt(id)) continue;
                            var e           = self.schedule_events[id2];
                            var start_time2 = moment(e.start_time, 'HH:mm a');
                            var end_time2   = moment(e.end_time, 'HH:mm a');
                            if(start_time.isBefore(end_time2) && end_time.isAfter(start_time2)) {
                                console.log('overlapped!!!');
                                return false;
                            }
                        }

                        event.appendTo($('.events-col'));
                        event.css('position','absolute');
                        event.css('top', top + 1);
                        event.css('left', left);

                        // set model time
                        if(event.hasClass('event-unpublished')) {
                            event.removeClass('event-unpublished');
                            event.addClass('event-published');
                            $('.ui-resizable-n', event).show();
                            $('.ui-resizable-s', event).show();
                            self.schedule_events[id] = self.unpublished_store.delete(id);
                        }
                        self.schedule_events[id].start_time = start_time.format('hh:mm a');
                        self.schedule_events[id].end_time   = end_time.format('hh:mm a');

                        if( typeof event.resizable( "instance" ) == 'undefined'){
                            self.createResizable(event);
                        }

                        $('.ui-resizable-n', event).attr('title', self.schedule_events[id].start_time);
                        $('.ui-resizable-s', event).attr('title', self.schedule_events[id].end_time);
                        //$('.ui-resizable-n', event).tooltip('option', 'content', self.schedule_events[id].start_time);
                        //$('.ui-resizable-s', event).tooltip('option', 'content', self.schedule_events[id].end_time);
                    }
                });
            });
        });

        createResizable(selector) {

            selector.each(function(){
                var element = $(this);
                var id      = element.attr('data-id');
                var top = $(".ui-resizable-n", element).tooltip({
                    tooltipClass: "tooltip-n-"+id,
                    track: true,
                    position: { my: "left+15 center", at: "right center"  }
                });
                var bottom = $(".ui-resizable-s", element).tooltip({
                    tooltipClass: "tooltip-s-"+id,
                    track: true,
                    position: { my: "left+15 center", at: "right center"  }
                });
                top.addClass('top');
                bottom.addClass('bottom');
            });

            selector.resizable({
                containment: ".events-col",
                maxWidth: self.slot_width,
                minWidth: self.slot_width,
                minHeight: self.minute_pixels * self.interval,
                maxHeight: null,
                grid: self.minute_pixels,
                handles: {
                    n: ".ui-resizable-n",
                    s: ".ui-resizable-s"
                },
                resize: function(e, ui) {

                    var element    = $(ui.element);
                    var id         = element.attr('data-id');
                    var size       = ui.size;
                    var pos        = element.offset();
                    var bottom     = pos.top + size.height;
                    var minutes    = ( parseInt(size.height) / self.minute_pixels);
                    var overlapped = false;
                    var container  = null;
                    var original_h = ui.originalSize.height;
                    ui.size.width  = ui.originalSize.width = self.slot_width;
                    console.log('position top ' + pos.top + ' height ' + size.height + ' bottom ' + bottom+' original_h '+original_h);
                    ui.position.left = ui.originalPosition.left;
                    // look for the current slot container that holds the begining of the current event
                    $('.time-slot-container').each(function(){
                        var top       = $(this).offset().top;
                        var bottom    = parseInt(top) + (self.minute_pixels * self.interval);
                        if(top <= pos.top &&  pos.top < bottom)
                        {
                            container = $(this);
                            console.log(' container top '+ top + ' container bottom '+bottom+ ' time '+container.attr('data-time'));
                            return false;
                        }
                    });

                    var start_time   = moment(container.attr('data-time'), 'HH:mm');
                    // calculate delta minutes...
                    if(pos.top > container.offset().top) {
                       var delta_minutes = ( pos.top - container.offset().top) / self.minute_pixels;
                       console.log(' adding '+ delta_minutes+' minutes to start time '+ container.attr('data-time'));
                       start_time = start_time.add('m', delta_minutes)
                    }

                    var end_time   = start_time.clone().add('m', minutes);
                    // check if we overlap with some former event ...
                    var overlapped_id = null;
                    for(var id2 in self.schedule_events)
                    {
                        if(parseInt(id2) === parseInt(id)) continue;
                        var event2      = self.schedule_events[id2];
                        var start_time2 = moment(event2.start_time, 'HH:mm a');
                        var end_time2   = moment(event2.end_time, 'HH:mm a');
                        if(start_time.isSameOrBefore(end_time2) && end_time.isSameOrAfter(start_time2)) {

                            overlapped    = true;
                            overlapped_id = parseInt(id2);
                            console.log('overlapped id '+overlapped_id+' !!!');
                            break;
                        }
                    }

                    if(overlapped)
                    {
                        var event2         = self.schedule_events[overlapped_id];
                        var start_time2    = moment(event2.start_time, 'HH:mm a');
                        var end_time2      = moment(event2.end_time, 'HH:mm a');
                        var delta1         = moment.duration(end_time2.diff(start_time)).asMinutes();
                        var delta2         = moment.duration(end_time.diff(start_time2)).asMinutes();
                        var delta          = delta1 < delta2 ? delta1 : delta2;
                        console.log(' delta minutes '+delta);
                        element.resizable( "option", "maxHeight", size.height - ( delta * self.minute_pixels ));
                        return false;
                    }

                    // set model time
                    self.schedule_events[id].start_time = start_time.format('hh:mm a');
                    self.schedule_events[id].end_time   = end_time.format('hh:mm a');
                    $('.ui-resizable-n', element).attr('title', self.schedule_events[id].start_time);
                    $('.ui-resizable-s', element).attr('title', self.schedule_events[id].end_time);
                    $('.ui-tooltip-content','.tooltip-n-'+id).html(self.schedule_events[id].start_time);
                    $('.ui-tooltip-content','.tooltip-s-'+id).html(self.schedule_events[id].end_time);
                },
                start: function( event, ui )
                {
                    var element = $(ui.element);
                    element.resizable( "option", "maxHeight", null );
                    var original_h = ui.originalSize.height;
                    console.log('start original_h '+original_h);
                },
                stop: function( event, ui ) {
                    var element = $(ui.element);
                    var id      = element.attr('data-id');
                    $('.ui-resizable-n', element).attr('title', self.schedule_events[id].start_time);
                    $('.ui-resizable-s', element).attr('title', self.schedule_events[id].end_time);
                    console.log('stop');
                }
            });
        }

        createDraggable(selector) {
            selector.draggable({
                containment: "document",
                cursor: "move",
                helper: "clone",
                opacity: 0.35
            });
        }

        this.api.on('ScheduleByDayAndLocationRetrieved',function(data) {
            console.log('ScheduleByDayAndLocationRetrieved');
            self.schedule_events = {};
            // update model
            for(var e of data.events) {
                self.schedule_events[e.id] = e;
            }
            // update UI
            $(".event-published").resizable("destroy");
            $(".event-published").draggable("destroy");
            $(".event-published").remove();

            self.update();

            self.createDraggable($(".event-published"));

            self.createResizable($(".event-published"));
        });

    </script>
</schedule-admin-view-published>