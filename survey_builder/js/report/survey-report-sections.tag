<survey-report-sections>
    <div class="survey_sections">
        <h2>Survey Sections</h2>
        <div class="section_container">
            <div class="section" each="{ section in report.Sections }">
                <span>{ section.Name }</span>
            </div>
        </div>
    </div>

    <script>
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
</survey-report-sections>