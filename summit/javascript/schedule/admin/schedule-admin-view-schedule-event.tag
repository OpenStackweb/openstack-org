<schedule-admin-view-schedule-event>
    <div class="event resizable event-published" id="event_{ data.id }" data-id="{ data.id }" style='position:absolute; top: { getEventTop() }; left: { getEventLeft() }; height: { getEventHeight() }'>
        <div class="ui-resizable-handle ui-resizable-n" title="{ data.start_time }">
            <span class="ui-icon ui-icon-triangle-1-n"></span>
        </div>
        <div class="event-inner-body">
            <div class="event-title">{ data.title }</div>
        </div>
        <div class="ui-resizable-handle ui-resizable-s" title="{ data.end_time }">
            <span class="ui-icon ui-icon-triangle-1-s"></span>
        </div>
    </div>

    <script>

    this.data          = opts.data;
    this.minute_pixels = parseInt(opts.minute_pixels);
    this.interval      = parseInt(opts.interval);
    var self           = this;

    this.on('mount', function() {
        $(function() {
        });
    });

    getEventTop() {
        var start_time    = moment(self.data.start_time, 'HH:mm a') ;
        var start_hour    = start_time.hour();
        var start_minutes = start_time.minute();
        var r             = start_minutes % self.interval;
        var start_minutes = start_minutes - r;
        var target        = $('#time_slot_container_'+self.pad(start_hour,2)+'_'+self.pad(start_minutes,2));
        var top           = parseInt(target.position().top) + ( r * self.minute_pixels);
        return top+'px';
    }

    getEventLeft() {
        var start_time    = moment(self.data.start_time, 'HH:mm a') ;
        var start_hour    = start_time.hour();
        var start_minutes = start_time.minute();
        var r             = start_minutes % self.interval;
        var start_minutes = start_minutes - r;
        var target        = $('#time_slot_container_'+self.pad(start_hour,2)+'_'+self.pad(start_minutes,2));
        var left          = target.position().left;
        return left+'px';
    }

    getEventHeight() {
        var start_time = moment(self.data.start_time, 'HH:mm a') ;
        var end_time   = moment(self.data.end_time, 'HH:mm a') ;
        var duration   = moment.duration(end_time.diff(start_time));
        var minutes    = duration.asMinutes();
        console.log('event duration '+ minutes);
        return (parseInt(minutes) * self.minute_pixels)+'px';
    }

    pad(num, size) {
        var s = num+"";
        while (s.length < size) s = "0" + s;
        return s;
    }

    </script>

</schedule-admin-view-schedule-event>