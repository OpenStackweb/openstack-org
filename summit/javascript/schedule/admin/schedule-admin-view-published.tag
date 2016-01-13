<schedule-admin-view-published>

            <div class="row">
                <div class="col-md-12">
                    <table id="day_schedule" class='unselectable'>
                    <tbody>
                        <tr>
                            <td class="times-col">
                                <div each={ time_slots } class="time-slot" id='time_slot_{ format("HH_mm") }'>{ format("hh:mm A") }</div>
                            </td>
                            <td class="events-col col-md-12">
                                <div each={ time_slots } class="time-slot-container" data-time="{ format('HH:mm') }" id="time_slot_container_{ format('HH_mm') }"></div>
                                <schedule-admin-view-schedule-event each="{ key, e in published_store.all()  }" data="{ e }" minute_pixels="{ parent.minute_pixels }" interval="{ parent.interval }"></schedule-admin-view-schedule-event>
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
        this.published_store   = opts.published_store;
        this.unpublished_store = opts.unpublished_store;
        this.summit            = summit;
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
                self.slot_width = $('.time-slot-container').width();
                $( ".time-slot-container" ).droppable({
                    hoverClass: "ui-state-hover",
                    accept: function(){
                        return self.published_store.currentDay() !== null && self.published_store.currentLocation();
                    },
                    drop: function( e, ui ) {

                        var element = $(ui.draggable);
                        var id      = parseInt(element.attr('data-id'));
                        var target  = $(this);

                        var top    = target.position().top;
                        var left   = target.position().left;

                        element.addClass('event-published');
                        var start_hour = target.attr('data-time');
                        var day        = self.published_store.currentDay();
                        var minutes    = (parseInt(element.css('height'))/ self.minute_pixels);
                        var end_time   = moment(day+' '+start_hour, 'YYYY-MM-DD HH:mm').add('m', minutes);
                        var start_time = moment(day+' '+start_hour, 'YYYY-MM-DD HH:mm');

                        console.log('star time '+ start_time.format('HH:mm'));
                        console.log('end time ' + end_time.format('HH:mm'));

                        for(var id2 in self.published_store.all())
                        {
                            if(parseInt(id2) === parseInt(id)) continue;
                            var e           = self.published_store.get(id2);
                            var start_time2 = e.start_datetime;
                            var end_time2   = e.end_datetime;
                            if(start_time.isBefore(end_time2) && end_time.isAfter(start_time2)) {
                                console.log('overlapped!!!');
                                return false;
                            }
                        }

                        element.appendTo($('.events-col'));
                        element.css('position','absolute');
                        element.css('top', top + 1);
                        element.css('left', left);

                        // set model time
                        if(element.hasClass('event-unpublished')) {
                            element.removeClass('event-unpublished');
                            element.addClass('event-published');
                            $('.ui-resizable-n', element).show();
                            $('.ui-resizable-s', element).show();
                            $('.unpublish-event-btn-container', element).show();
                            self.published_store.add(self.unpublished_store.delete(id));
                        }
                        var event            = self.published_store.get(id);
                        event.start_datetime = start_time;
                        event.end_datetime   = end_time;

                        if( typeof element.resizable( "instance" ) == 'undefined'){
                            self.createResizable(element);
                        }

                        $('.ui-resizable-n', element).attr('title', event.start_datetime.format('hh:mm a'));
                        $('.ui-resizable-s', element).attr('title', event.end_datetime.format('hh:mm a'));

                        event.location_id = self.published_store.currentLocation();
                        self.api.publish(self.summit.id, event);
                    }
                });

                $( "body" ).on( "click", ".unpublish-event-btn",function() {
                    var id = $(this).attr('data-event-id');
                    swal({
                        title: "Are you sure?",
                        text: "to unpublish this event from summit schedule!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, unpublish it!",
                        closeOnConfirm: false
                        }, function(){
                            self.dispatcher.unPublishEvent(self.summit.id, id);
                            $('#event_'+id).remove();
                            swal("Deleted!", "Your event was unpublished.", "success");
                        });
                });
            });

            $( window ).resize(function() {
                self.slot_width = $('.time-slot-container').width();
                $(".event-published").css('width', self.slot_width);
                $(".ui-resizable").resizable( "option", "maxWidth", self.slot_width );
                $(".ui-resizable").resizable( "option", "minWidth", self.slot_width );
            });
        });

        createResizable(selector) {

            selector.each(function(){
                var element = $(this);
                var id      = element.attr('data-id');
                var top = $(".ui-resizable-n", element).uitooltip({
                    tooltipClass: "tooltip-n-"+id,
                    track: true,
                    position: { my: "left+15 center", at: "right center"  }
                });
                var bottom = $(".ui-resizable-s", element).uitooltip({
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

                    var element      = $(ui.element);
                    var id           = element.attr('data-id');
                    var size         = ui.size;
                    var pos          = element.offset();
                    var bottom       = pos.top + size.height;
                    var minutes      = ( parseInt(size.height) / self.minute_pixels);
                    var overlapped   = false;
                    var container    = null;
                    var original_h   = ui.originalSize.height;
                    ui.size.width    = ui.originalSize.width = self.slot_width;
                    ui.position.left = ui.originalPosition.left;
                    console.log('position top ' + pos.top + ' height ' + size.height + ' bottom ' + bottom+' original_h '+original_h);

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
                    var day        = self.published_store.currentDay();
                    var start_hour = container.attr('data-time');
                    var start_time = moment(day+' '+start_hour, 'YYYY-MM-DD HH:mm');
                    // calculate delta minutes...
                    if(pos.top > container.offset().top) {
                       var delta_minutes = ( pos.top - container.offset().top) / self.minute_pixels;
                       console.log(' adding '+ delta_minutes+' minutes to start time '+ container.attr('data-time'));
                       start_time = start_time.add('m', delta_minutes)
                    }

                    var end_time   = start_time.clone().add('m', minutes);
                    // check if we overlap with some former event ...
                    var overlapped_id = null;
                    for(var id2 in self.published_store.all())
                    {
                        if(parseInt(id2) === parseInt(id)) continue;
                        var event2      = self.published_store.get(id2);
                        var start_time2 = event2.start_datetime;
                        var end_time2   = event2.end_datetime;
                        if(start_time.isSameOrBefore(end_time2) && end_time.isSameOrAfter(start_time2)) {

                            overlapped    = true;
                            overlapped_id = parseInt(id2);
                            console.log('overlapped id '+overlapped_id+' !!!');
                            break;
                        }
                    }

                    if(overlapped)
                    {
                        var event2         = self.published_store.get(overlapped_id);
                        var start_time2    = event2.start_datetime;
                        var end_time2      = event2.end_datetime;
                        var delta1         = moment.duration(end_time2.diff(start_time)).asMinutes();
                        var delta2         = moment.duration(end_time.diff(start_time2)).asMinutes();
                        var delta          = delta1 < delta2 ? delta1 : delta2;
                        console.log(' delta minutes '+delta);
                        element.resizable( "option", "maxHeight", size.height - ( ( delta ) * self.minute_pixels ));
                        return false;
                    }

                    // set model time
                    var event            = self.published_store.get(id);
                    event.start_datetime = start_time;
                    event.end_datetime   = end_time;
                    $('.ui-resizable-n', element).attr('title', event.start_datetime.format('hh:mm a'));
                    $('.ui-resizable-s', element).attr('title', event.end_datetime.format('hh:mm a'));
                    $('.ui-tooltip-content','.tooltip-n-'+id).html(event.start_datetime.format('hh:mm a'));
                    $('.ui-tooltip-content','.tooltip-s-'+id).html(event.end_datetime.format('hh:mm a'));
                },
                start: function( e, ui )
                {
                    var element = $(ui.element);
                    element.resizable( "option", "maxHeight", null );
                    var original_h = ui.originalSize.height;
                    console.log('start original_h '+original_h);
                },
                stop: function( e, ui ) {
                    var element = $(ui.element);
                    var id      = element.attr('data-id');
                    var event   = self.published_store.get(id);
                    $('.ui-resizable-n', element).attr('title', event.start_datetime.format('hh:mm a'));
                    $('.ui-resizable-s', element).attr('title', event.end_datetime.format('hh:mm a'));
                    event.location_id = self.published_store.currentLocation();
                    self.api.publish(self.summit.id, event);
                    console.log('stop');
                }
            });
        }

        createDraggable(selector) {
            selector.draggable({
                containment: "document",
                cursor: "move",
                helper: "clone",
                opacity: 0.5
            });
        }

        self.published_store.on(self.published_store.LOAD_STORE,function() {
            console.log('UI: '+self.published_store.LOAD_STORE);
            // update UI
            $(".event-published").resizable("destroy");
            $(".event-published").draggable("destroy");
            $(".event-published").remove();

            self.update();
            $(".event-published").css('width', $('.time-slot-container').width());
            self.createDraggable($(".event-published"));

            self.createResizable($(".event-published"));

            $('[data-toggle="popover"]').popover({
                trigger: 'hover focus',
                html: true,
                container: 'body',
                placement: 'auto',
                animation: true,
                template : '<div class="popover" role="tooltip"><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
            });

            $('body').ajax_loader('stop');
        });

    </script>
</schedule-admin-view-published>