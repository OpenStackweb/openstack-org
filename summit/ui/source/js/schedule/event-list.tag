<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<event-list>
    <div class="col-md-12 speaker-events-div">
        <schedule-event each={ summit.events } show_date={ true } ></schedule-event>
    </div>

    <script>

        this.summit                   = opts.summit;
        this.search_url               = opts.search_url;
        this.schedule_api             = opts.schedule_api;
        this.base_url                 = opts.base_url;
        this.default_event_color      = opts.default_event_color;
        this.clicked_event            = {};
        var self                      = this;

        this.on('mount', function(){
               $(document).off("click",".btn-go-event").on("click",".btn-go-event", function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    var event_url = $(this).attr('href');
                    var url       = new URI(event_url);
                    // add back url
                    $(window).url_fragment('setParam','eventId', $(this).data('event-id'));
                    window.location.hash = $(window).url_fragment('serialize');
                    url.addQuery('BackURL', window.location)
                    window.location = url.toString();
                    return false;
               });
        });

        this.schedule_api.on('eventAdded2MySchedule',function(event_id) {
            console.log('eventAdded2MySchedule');
            self.clicked_event[event_id].own = true;
            self.update();
            delete self.clicked_event[event_id];
        });

        this.schedule_api.on('eventRemovedFromMySchedule',function(event_id) {
            console.log('eventRemovedFromMySchedule');
            self.clicked_event[event_id].own = false;
            self.update();
            delete self.clicked_event[event_id];
        });

    </script>
</event-list>