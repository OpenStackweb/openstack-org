<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>

<reports-admin-container>
    <div class="row">
        <div class="col-md-4" if={ show_search }>
            <div class="input-group" style="width: 100%;">
                <input data-rule-required="true" data-rule-minlength="3" id="search-term" style="height:33px;" class="form-control input-global-search" placeholder="Search...">
                <span class="input-group-btn" style="width: 5%;">
                    <button class="btn btn-default btn-global-search" onclick={ searchReport }><i class="fa fa-search"></i></button>
                    <button class="btn btn-default btn-global-search-clear" onclick={ clearSearch }>
                        <i class="fa fa-times"></i>
                    </button>
                </span>
            </div>
        </div>
        <div class="col-md-2" if={ show_status_filter }>
            <select id="status-filter" class="form-control" onchange={ searchReport }>
                <option value="all">All</option>
                <option value="hide_confirmed">Hide Confirmed</option>
                <option value="hide_registered">Hide Registered</option>
                <option value="hide_checkedin">Hide Checked In</option>
                <option value="hide_all">Hide Both</option>
            </select>
        </div>
        <div class="col-md-4" if={ show_presentation_status_filter }>
            <select id="presentation-status-filter" class="form-control" onchange={ searchReport }>
                <option value="all">All</option>
                <option value="received">Received</option>
                <option value="null">Null</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary" id="export-report" if={ show_export } onclick={ exportReport } >Export</button>
            <button class="btn btn-success" id="save-report" if={ show_save } onclick={ saveReport } >Save</button>
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
    <reports-admin-presentations-by-track-report if={ report == 'presentations_by_track_report' } page_limit="{ limit }" summit_id="{ summit_id }" tracks="{ tracks }" dispatcher="{ dispatcher }"></reports-admin-presentations-by-track-report>

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
            self.toggleFilters();
            $("#search-term").val('');
            self.update();

            $("#search-term").keydown(function (e) {
                if (e.keyCode == 13) {
                    self.searchReport();
                } 
            });

        });

        saveReport(e) {
            if (typeof(e) !== 'undefined') {
                e.preventUpdate = true;
            }
            self.dispatcher.saveReport(self.report);
        }

        exportReport(e) {
            if (typeof(e) !== 'undefined') {
                e.preventUpdate = true;
            }
            self.dispatcher.exportReport(self.report);
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
            self.dispatcher.getReport(self.report);
        }

        clearSearch() {
            $('#search-term').val('');
            self.dispatcher.getReport(self.report);
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
                case 'presentations_by_track_report':
                    self.show_search = true;
                    self.show_export = true;
                    break;
            }
        }

    </script>

</reports-admin-container>