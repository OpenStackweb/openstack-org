<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<reports-admin-container>
    <div class="row">
        <div class="col-md-4">
            <select id="report_select" class="form-control" >
                <option value="speaker_report" selected> Speaker Report </option>
                <option value="room_report"> Speakers Per Room </option>
            </select>
        </div>
        <div class="col-md-4">
            <button class="btn btn-primary" id="export-report">Export CSV</button>
        </div>
    </div>
    <br>

    <reports-admin-speaker-report if={report == 'speaker_report'} page_limit="{ limit }" summit_id="{ summit_id }"></reports-admin-speaker-report>
    <reports-admin-room-report if={report == 'room_report'} summit_id="{ summit_id }"></reports-admin-room-report>

    <script>
        this.report     = opts.report;
        this.summit_id  = opts.summit_id;
        this.limit      = opts.limit;
        var self        = this;

        this.on('mount', function() {
            $("#export-report").click(function(){
                $("#report-table").tableToCSV($('#report_select option:selected').val());
            });

            $("#report_select").change(function(){
                self.report = $(this).val();
                self.update();
            });

        });

    </script>

</reports-admin-container>