<survey-report-filters>

    <div class="report_global_filters">
        <div class="container">
            <div class="row">
                <div class="report_global_filters_title">GLOBAL FILTERS</div>
                <div class="report_clear_filters">
                    <span class="fa-stack fa-lg">
                        <i class="fa fa-circle fa-stack-2x"></i>
                        <i class="fa fa-times fa-stack-1x fa-inverse"></i>
                    </span>
                    clear all filters
                </div>
            </div>
            <div class="row">
                <div class="report_filter_box { last:((parent.filters.length-1) == i) }" each="{ filter,i in filters }">
                    <select data-qid="{ filter.Question }" class="report_filter form-control">
                        <option value="" disabled selected>{ filter.Label }</option>
                        <option each="{ option in filter.Options }" value='{ option }'>{ option }</option>
                    </select>
                </div>
            </div>
        </div>
    </div>


    <script>
        this.filters   = null;
        this.dispatcher = opts.dispatcher;
        this.api        = opts.api;
        var self        = this;

        this.on('mount', function(){
            $("body").on("change",".report_filter",function(){
                self.api.getReport();
            });

            $("body").on("click",".report_clear_filters",function(){
                $('.report_filter').each(function(){
                    $(this).val('');
                });
                self.api.getReport();
            });
        });

        self.api.on(self.api.TEMPLATE_RETRIEVED, function(template)
        {
            self.filters = template.Filters;
            self.update();

        });



    </script>
</survey-report-filters>