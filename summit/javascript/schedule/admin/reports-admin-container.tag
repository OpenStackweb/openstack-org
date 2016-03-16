<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<reports-admin-container>
    <div class="row">
        <div class="col-md-4">
            <select id="report_select" class="form-control" >
                <option value="speaker_report"> Speaker Report </option>
                <option value="presentation_report" selected> Presentation Report </option>
                <option value="room_report"> Speakers Per Room </option>
            </select>
        </div>
        <div class="col-md-4">
            <button class="btn btn-primary" id="export-report" onclick={ exportReport } >Export CSV</button>
            <button class="btn btn-success" id="save-report" onclick={ saveReport } >Save</button>
        </div>
        <div class="col-md-4" if={report == 'presentation_report'}>
            <div class="input-group" style="width: 100%;">
                <input data-rule-required="true" data-rule-minlength="3" type="text" id="search-term" class="form-control input-global-search" placeholder="Search Speaker or Presentation">
                <span class="input-group-btn" style="width: 5%;">
                    <button class="btn btn-default btn-global-search" onclick={ searchReport }><i class="fa fa-search"></i></button>
                    <button class="btn btn-default btn-global-search-clear" onclick={ clearSearch }>
                        <i class="fa fa-times"></i>
                    </button>
                </span>
            </div>
        </div>
    </div>
    <br>

    <reports-admin-speaker-report if={report == 'speaker_report'} page_limit="{ limit }" summit_id="{ summit_id }" dispatcher="{ dispatcher }"></reports-admin-speaker-report>
    <reports-admin-presentation-report if={report == 'presentation_report'} page_limit="{ limit }" summit_id="{ summit_id }" dispatcher="{ dispatcher }"></reports-admin-presentation-report>
    <reports-admin-room-report if={report == 'room_report'} summit_id="{ summit_id }" dispatcher="{ dispatcher }"></reports-admin-room-report>

    <script>
        this.report     = opts.report;
        this.dispatcher = opts.dispatcher;
        this.summit_id  = opts.summit_id;
        this.limit      = opts.limit;
        var self        = this;

        this.on('mount', function() {
            $("#report_select").change(function(){
                self.report = $(this).val();
                self.update();
            });

            $("#search-term").keydown(function (e) {
                if (e.keyCode == 13) {
                    self.searchReport();
                }
            });

        });

        saveReport(e) {
            var report = $('#report_select').val();
            e.preventUpdate = true;
            self.dispatcher.saveReport(report);
        }

        exportReport() {
            var report = $('#report_select').val();
            var sort = $('.sorted').data('sort');
            var sort_dir = $('.sorted').data('dir');
            $('body').ajax_loader();

            $.get('api/v1/summits/'+self.summit_id+'/reports/export/'+report,
                {sort:sort, sort_dir:sort_dir},
                function(csv){
                    $('body').ajax_loader('stop');
                    var uri = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
                    var download_link = document.createElement('a');
                    download_link.href = uri;
                    download_link.download = report+".csv";
                    document.body.appendChild(download_link);
                    download_link.click();
                    document.body.removeChild(download_link);
            });
        }

        toggleSort(elem) {
            var sort_dir = (elem.data('dir') == 'ASC') ? 'DESC' : 'ASC';
            elem.data('dir',sort_dir);
            $('.fa','.sortable').remove();
            $('.sorted').removeClass('sorted');

            var arrow = (sort_dir == 'ASC') ? '<i class="fa fa-caret-up"></i>' : '<i class="fa fa-caret-down"></i>';
            elem.html(elem.text()+arrow);
            elem.addClass('sorted');
        }

        searchReport() {
            var report = $('#report_select').val();
            self.dispatcher.getReport(report);
        }

        clearSearch() {
            $('#search-term').val('');
            var report = $('#report_select').val();
            self.dispatcher.getReport(report);
        }

    </script>

</reports-admin-container>