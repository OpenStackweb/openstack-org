<survey-report-filters>
    <div class="container">
        <div class="report_templates_container">
            <select id="report-templates">
                <option each="{ list in templates }" value='{ list.id }'>{ list.title }</option>
            </select>
        </div>
    </div>
    <div class="report_global_filters">
        <div class="container">
            <div class="row">
                <div class="col-md-2">GLOBAL FILTERS</div>
                <div class="col-md-2"><a href="">Clear Filters</a></div>
            </div>
            <div class="row">
                <div class="col-md-2" each="{ filter in report.Filters }">
                    <select id="report-filter-{ filter.Filter.ID }" class="report_filter">
                        <option value="" disabled selected>{ filter.Label }</option>
                        <option each="{ option in filter.Options }" value='{ option }'>{ option }</option>
                    </select>
                </div>
            </div>
        </div>
    </div>


    <script>
        this.templates  = opts.templates;
        this.report     = null;
        var self        = this;

        this.on('mount', function(){
            var template_id = $('#report-templates').val();
            self.getReport(template_id);
        });

        getReport(template_id) {
            $('body').ajax_loader();

            $.getJSON('api/v1/surveys/report/'+template_id,{},function(data){
                self.report = data;
                self.update();
                $('body').ajax_loader('stop');
            });
        }

    </script>
</survey-report-filters>