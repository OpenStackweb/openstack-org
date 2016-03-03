<schedule-admin-view-schedule-event>
    <div class="event resizable event-published unselectable { getCSSClassBySelectionStatus(data.status) }" id="event_{ data.id }" data-id="{ data.id }" style='position:absolute; top: { getEventTop() }; left: { getEventLeft() }; height: { getEventHeight() }'>
        <div class="ui-resizable-handle ui-resizable-n" title="{ data.start_datetime.format('hh:mm a') }">
            <span class="ui-icon ui-icon-triangle-1-n"></span>
        </div>
        <div class="event-buttons">
            <a class="unpublish-event-btn" title="unpublish event" data-event-id="{ data.id }"><i class="fa fa-times"></i></a>
            <a href="summit-admin/{ parent.summit.id }/events/{ data.id }" class="edit-event-btn" title="edit event">
                <i class="fa fa-pencil-square-o"></i>
            </a>
        </div>
        <div class="event-inner-body">
            <div>
                 <a id="popover_{ data.id }" data-content="{ getPopoverContent() }" title="{ data.title }" data-toggle="popover">{ data.title.substring(0, 70) }{ data.title.length > 70 ? '...':''}{ data.class_name === 'Presentation'?' - '+parent.summit.tracks_dictionary[data.track_id].name:'' }</a>
            </div>
            <div class="presentation-status">
                <div if={ data.status }  class="event-status-component" title="status"><i class="fa fa-check-circle">&nbsp;{data.status}</i></div>
                <div if={ data.headcount } class="event-status-component" title="headcount">&nbsp;<i class="fa fa-users">&nbsp;{data.headcount}&nbsp;of&nbsp;{ parent.summit.locations_dictionary[data.location_id].capacity }</i></div>
                <div if={ data.attendees_schedule_count } class="event-status-component" title="# Added to Schedule">&nbsp;<i class="fa fa-calendar-check-o">&nbsp;{ data.attendees_schedule_count }</i></div>
            </div>
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
        var description = self.data.abstract != null ? self.data.abstract : self.data.description;
        if(description == null) description = 'TBD';
        var res = '<div class="row"><div class="col-md-12">'+description+'</div></div>';
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

    getCSSClassBySelectionStatus(status) {
        switch(status){
            case 'accepted':return 'status-accepted';break;
            case 'alternate':return 'status-alternate';break;
            case 'unaccepted':return 'status-unaccepted';break;
            default: return '';break;
        }
        return '';
    }

    </script>

</schedule-admin-view-schedule-event>