<schedule-admin-view-schedule-event>
    <div class="event resizable event-published" id="event_{ data.id }" style='position:absolute; top: { getEventTop() }; left: { getEventLeft() }; height: { getEventHeight() }'>
        <div class="ui-resizable-handle ui-resizable-n">
            <span class="ui-icon ui-icon-triangle-1-n"></span>
        </div>
        <div class="event-title">{ data.title }</div>
        <div class="event-duration">{ data.start_time } - { data.end_time }</div>
        <div class="ui-resizable-handle ui-resizable-s">
            <span class="ui-icon ui-icon-triangle-1-s"></span>
        </div>
    </div>

    <script>

    this.data = opts.data;
    var self  = this;

    this.on('mount', function() {
        $(function() {
        });
    });

    getEventTop() {
        var start_time    = moment(self.data.start_time, 'HH:mm a') ;
        var start_hour    = start_time.hour();
        var start_minutes = start_time.minute();
        var r             = start_minutes % 15;
        var start_minutes = start_minutes - r;
        var target        = $('#time_slot_container_'+self.pad(start_hour,2)+'_'+self.pad(start_minutes,2));
        var top           = parseInt(target.position().top) + ( r * 3);
        return top+'px';
    }

    getEventLeft() {
        var start_time    = moment(self.data.start_time, 'HH:mm a') ;
        var start_hour    = start_time.hour();
        var start_minutes = start_time.minute();
        var r             = start_minutes % 15;
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
        return (parseInt(minutes) * 3)+'px';
    }

    pad(num, size) {
        var s = num+"";
        while (s.length < size) s = "0" + s;
        return s;
    }

    </script>

</schedule-admin-view-schedule-event>