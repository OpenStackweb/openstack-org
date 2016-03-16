<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<reports-admin-room-report>

    <div class="panel panel-default" each="{ key, day in report_data }">
        <div class="panel-heading">{ key }</div>

        <table class="table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Code</th>
                    <th>Event</th>
                    <th>Room</th>
                    <th>Speakers</th>
                    <th>HeadCount</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr each={ event in day } data-id="{ event.id }">
                    <td>{ event.start_time } - { event.end_time }</td>
                    <td>{ event.code }</td>
                    <td>{ event.title }</td>
                    <td>{ event.room }</td>
                    <td>{ event.speakers }</td>
                    <td><input type="text" class="headcount" value={ event.headcount } /></td>
                    <td>{ event.total }</td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        this.dispatcher      = opts.dispatcher;
        this.summit_id       = opts.summit_id;
        this.report_data     = [];
        var self             = this;


        this.on('mount', function() {
            self.getReport();

            $('.reports-wrapper').on('change','input',function(){
                $(this).parents('tr').addClass('changed');
            });
        });

        getReport() {
            $('body').ajax_loader();

            $.getJSON('api/v1/summits/'+self.summit_id+'/reports/room_report', {}, function(data){
                self.report_data = data;
                self.update();
                $('body').ajax_loader('stop');
            });
        }

        self.dispatcher.on(self.dispatcher.SAVE_ROOM_REPORT,function(report) {
            var request = [];
            $('.changed').each(function(){
                var id = $(this).data('id');
                var headcount = $('.headcount',this).val();
                request.push({id: id, headcount: headcount});
            });

            if (request.length) {
                $.ajax({
                    type: 'PUT',
                    url: 'api/v1/summits/'+self.summit_id+'/reports/save_report/'+report,
                    data: JSON.stringify(request),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json"
                }).done(function(data) {
                    $('.changed').removeClass('changed');
                    swal('Updated', 'Changes saved.', 'success');
                }).fail(function(jqXHR) {
                    var responseCode = jqXHR.status;
                    if(responseCode == 412) {
                        var response = $.parseJSON(jqXHR.responseText);
                        swal('Validation error', response.messages[0].message, 'warning');
                    } else {
                        swal('Error', 'There was a problem saving the event, please contact admin.', 'warning');
                    }
                });
            }
        });

    </script>

</reports-admin-room-report>