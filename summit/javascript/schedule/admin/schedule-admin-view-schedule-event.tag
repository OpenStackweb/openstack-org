<schedule-admin-view-schedule-event>
    <div class="event resizable event-published unselectable" id="event_{ data.id }" data-id="{ data.id }" style='position:absolute; top: { getEventTop() }; left: { getEventLeft() }; height: { getEventHeight() }'>
        <div class="ui-resizable-handle ui-resizable-n" title="{ data.start_datetime.format('hh:mm a') }">
            <span class="ui-icon ui-icon-triangle-1-n"></span>
        </div>
        <div>
            <button class="btn btn-danger btn-xs unpublish-event-btn" title="unpublish event" data-event-id="{ data.id }"><i class="fa fa-times"></i></button>
        </div>
        <div class="event-inner-body">
            <a id="popover_{ data.id }" data-content="{ getPopoverContent() }" title="{ data.title }" data-toggle="popover">{ data.title }</a>
        </div>
        <div class="ui-resizable-handle ui-resizable-s" title="{ data.end_datetime.format('hh:mm a') }">
            <span class="ui-icon ui-icon-triangle-1-s"></span>
        </div>
    </div>

    <script>

    this.data          = opts.data;
    this.dispatcher    = parent.dispatcher;
    this.summit        = parent.summit;
    this.minute_pixels = parseInt(opts.minute_pixels);
    this.interval      = parseInt(opts.interval);
    var self           = this;

    this.on('mount', function() {
        $(function() {
        });
    });

    getEventTop() {
        var start_time    = self.data.start_datetime;
        var start_hour    = start_time.hour();
        var start_minutes = start_time.minute();
        var r             = start_minutes % self.interval;
        var start_minutes = start_minutes - r;
        var target        = $('#time_slot_container_'+self.pad(start_hour,2)+'_'+self.pad(start_minutes,2));
        var top           = parseInt(target.position().top) + ( r * self.minute_pixels);
        return top+'px';
    }

    getEventLeft() {
        var start_time    = self.data.start_datetime;
        var start_hour    = start_time.hour();
        var start_minutes = start_time.minute();
        var r             = start_minutes % self.interval;
        var start_minutes = start_minutes - r;
        var target        = $('#time_slot_container_'+self.pad(start_hour,2)+'_'+self.pad(start_minutes,2));
        var left          = target.position().left;
        return left+'px';
    }

    getEventHeight() {
        var start_time = self.data.start_datetime;
        var end_time   = self.data.end_datetime;
        var duration   = moment.duration(end_time.diff(start_time));
        var minutes    = duration.asMinutes();
        console.log('event duration '+ minutes);
        return (parseInt(minutes) * self.minute_pixels)+'px';
    }

    getPopoverContent() {
        var res = '<div class="row"><div class="col-md-12">'+self.data.description+'</div></div>';
        if(typeof(self.data.speakers) !== 'undefined') {
            res += '<div class="row"><div class="col-md-12"><b>Speakers</b></div></div>';
            for(var idx in self.data.speakers) {
                var speaker = self.data.speakers[idx];
                res += '<div class="row"><div class="col-md-12">'+ speaker.name+'</div></div>';
            }
        }
        return res;
    }

    pad(num, size) {
        var s = num+"";
        while (s.length < size) s = "0" + s;
        return s;
    }

    </script>

</schedule-admin-view-schedule-event>