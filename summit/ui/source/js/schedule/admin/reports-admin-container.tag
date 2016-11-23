<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<reports-admin-container>
    <div class="row">
        <div class="col-md-4">
            <select id="report_select" class="form-control" >
                <option value="presentation_report" selected> Presentation Report </option>
                <option value="presentations_company_report"> Presentations by Company </option>
                <option value="rsvp_report"> RSVP Report </option>
                <option value="room_report"> Speakers Per Room </option>
                <option value="speaker_report"> Speaker Report </option>
                <option value="track_questions_report"> Track Questions Report </option>
                <option value="video_report"> Video Output List </option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary" id="export-report" if={ show_export } onclick={ exportReport } >Export</button>
            <button class="btn btn-success" id="save-report" if={ show_save } onclick={ saveReport } >Save</button>
        </div>
        <div class="col-md-2" if={ show_status_filter }>
            <select id="status-filter" class="form-control" onchange={ searchReport }>
                <option value="all">All</option>
                <option value="hide_confirmed">Hide Confirmed</option>
                <option value="hide_registered">Hide Registered</option>
                <option value="hide_both">Hide Both</option>
            </select>
        </div>
        <div class="col-md-4" if={ show_search }>
            <div class="input-group" style="width: 100%;">
                <input data-rule-required="true" data-rule-minlength="3" type="text" id="search-term" class="form-control input-global-search" placeholder="Search...">
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
    <reports-admin-rsvp-report if={ report == 'rsvp_report' } base_url="{ base_url }" page_limit="{ limit }" summit_id="{ summit_id }" dispatcher="{ dispatcher }"></reports-admin-rsvp-report>
    <reports-admin-track-questions-report if={ report == 'track_questions_report' } page_limit="{ limit }" summit_id="{ summit_id }" dispatcher="{ dispatcher }"></reports-admin-track-questions-report>
    <reports-admin-presentations-company-report if={ report == 'presentations_company_report' } page_limit="{ limit }" summit_id="{ summit_id }" dispatcher="{ dispatcher }"></reports-admin-presentations-company-report>

    <script>
        this.report             = opts.report;
        this.dispatcher         = opts.dispatcher;
        this.summit_id          = opts.summit_id;
        this.limit              = opts.limit;
        this.locations          = opts.locations;
        this.tracks             = opts.tracks;
        this.base_url           = opts.base_url;
        this.show_search        = true;
        this.show_status_filter = true;
        this.show_save          = true;
        this.show_export        = true;
        var self                = this;

        this.on('mount', function() {
            $("#report_select").change(function(){
                self.report = $(this).val();
                self.toggleFilters();
                $("#search-term").val('');
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

        toggleFilters() {
            self.show_status_filter = false;
            self.show_search = false;
            self.show_save = false;
            self.show_export = false;

            switch (self.report) {
                case 'speaker_report':
                    self.show_status_filter = true;
                    self.show_save = true;
                    self.show_export = true;
                    break;
                case 'presentation_report':
                    self.show_status_filter = true;
                    self.show_search = true;
                    self.show_save = true;
                    self.show_export = true;
                    break;
                case 'rsvp_report':
                    self.show_search = true;
                    self.show_export = true;
                    break;
                case 'track_questions_report':
                    self.show_search = true;
                    break;
                case 'room_report':
                    self.show_save = true;
                    self.show_export = true;
                    break;
                case 'video_report':
                    self.show_save = true;
                    self.show_export = true;
                    break;
                case 'presentations_company_report':
                    self.show_search = true;
                    self.show_export = true;
                    break;
            }
        }

    </script>

</reports-admin-container>