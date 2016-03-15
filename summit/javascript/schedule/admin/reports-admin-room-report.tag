<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<reports-admin-room-report>

    <div class="panel panel-default" each="{ key, day in data }">
        <div class="panel-heading">{ key }</div>

        <table class="table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Code</th>
                    <th>Event</th>
                    <th>Speakers</th>
                    <th>Room</th>
                </tr>
            </thead>
            <tbody>
                <tr each={ event in day }>
                    <td>{ event.start_time } - { event.end_time }</td>
                    <td>{ event.code }</td>
                    <td>{ event.title }</td>
                    <td>{ event.speakers }</td>
                    <td>{ event.room }</td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        this.dispatcher      = opts.dispatcher;
        this.summit_id       = opts.summit_id;
        this.data            = [];
        var self             = this;


        this.on('mount', function() {
            self.getReport();
        });

        getReport() {
            $('body').ajax_loader();

            $.getJSON('api/v1/summits/'+self.summit_id+'/reports/room_report', {}, function(data){
                self.data = data;
                self.update();
                $('body').ajax_loader('stop');
            });
        }

    </script>

</reports-admin-room-report>