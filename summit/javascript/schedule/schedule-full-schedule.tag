<raw>
    this.root.innerHTML = opts.content
</raw>

<schedule-full-schedule>

    <div class="panel panel-default" each="{ key, day in events }">
        <div class="panel-heading">{ key }</div>

        <table class="table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Event</th>
                    <th>Room</th>
                    <th>RSVP</th>
                </tr>
            </thead>
            <tbody>
                <tr each={ event in day } data-id="{ event.id }">
                    <td>{ event.start_time } - { event.end_time }</td>
                    <td>
                        <a href="{ base_url+'events/'+ event.id }" target="_blank">{ event.title }</a><br>
                        <div class="event_description" style="display:none">
                            <raw content="{ event.description }"/>
                        </div>
                    </td>
                    <td if={ should_show_venues == 1 }>{ event.room }</td>
                    <td if={ should_show_venues == 0 }>TBD</td>
                    <td>
                        <a href="{ event.rsvp }" if={ event.rsvp != ''}>RSVP</a>
                        <span if={ event.rsvp == '' }> - </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        this.events             = opts.events;
        this.summit_id          = opts.summit_id;
        this.should_show_venues = opts.should_show_venues
        this.view               = 'days';
        this.base_url           = opts.base_url;
        var self                = this;


        this.on('mount', function() {
            $('#full-schedule-filter').change(function(){
                self.view = $(this).val();
                self.pullSchedule();
            });

            $('#show_desc').change(function(){
                $(this).toggleClass('active');
                $('.event_description').toggle();
            });

        });

        pullSchedule() {
            $('body').ajax_loader();

            $.getJSON('api/v1/summits/'+self.summit_id+'/schedule/full',{sort:self.view},function(data){
                self.events = data;
                self.update();
                $('body').ajax_loader('stop');
            });
        }


    </script>

</schedule-full-schedule>