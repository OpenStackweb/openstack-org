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
                <option value="video_report"> Video Output List </option>
                <option value="rsvp_report"> RSVP Report </option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary" id="export-report" onclick={ exportReport } >Export</button>
            <button class="btn btn-success" id="save-report" onclick={ saveReport } >Save</button>
        </div>
        <div class="col-md-2" if={report == 'presentation_report' || report == 'speaker_report'}>
            <select id="status-filter" class="form-control" onchange={ searchReport }>
                <option value="all">All</option>
                <option value="hide_confirmed">Hide Confirmed</option>
                <option value="hide_registered">Hide Registered</option>
                <option value="hide_both">Hide Both</option>
            </select>
        </div>
        <div class="col-md-4" if={report == 'presentation_report' || report == 'rsvp_report'}>
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

    <reports-admin-speaker-report if={ report == 'speaker_report' } page_limit="{ limit }" summit_id="{ summit_id }" dispatcher="{ dispatcher }"></reports-admin-speaker-report>
    <reports-admin-presentation-report if={ report == 'presentation_report' } page_limit="{ limit }" summit_id="{ summit_id }" dispatcher="{ dispatcher }"></reports-admin-presentation-report>
    <reports-admin-room-report if={ report == 'room_report' } summit_id="{ summit_id }" locations="{ locations }" dispatcher="{ dispatcher }"></reports-admin-room-report>
    <reports-admin-video-report if={ report == 'video_report' } summit_id="{ summit_id }" locations="{ locations }" tracks="{ tracks }" dispatcher="{ dispatcher }"></reports-admin-video-report>
    <reports-admin-rsvp-report if={ report == 'rsvp_report' } page_limit="{ limit }" summit_id="{ summit_id }" dispatcher="{ dispatcher }"></reports-admin-rsvp-report>

    <script>
        this.report     = opts.report;
        this.dispatcher = opts.dispatcher;
        this.summit_id  = opts.summit_id;
        this.limit      = opts.limit;
        this.locations  = opts.locations;
        this.tracks     = opts.tracks;
        var self        = this;

        this.on('mount', function() {
            $("#report_select").change(function(){
                self.report = $(this).val();
                console.log('selected report '+self.report);
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
            if (typeof(e) !== 'undefined') {
                e.preventUpdate = true;
            }
            self.dispatcher.saveReport(report);
        }

        exportReport(e) {
            var report = $('#report_select').val();
            if (typeof(e) !== 'undefined') {
                e.preventUpdate = true;
            }
            self.dispatcher.exportReport(report);
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