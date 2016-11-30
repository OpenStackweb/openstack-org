<survey-report-sections>
    <div class="survey_sections">
        <h2>Survey Sections</h2>
        <div class="section_container">
            <div class="section { active:(i == 0) }" data-section-id="{ section.ID }" each="{ section,i in sections }">
                <span>{ section.Name }</span>
                <span><i class="fa fa-chevron-circle-right"></i></span>
            </div>
        </div>
        <div class="section_container">
            <br>
            <div class="section" data-section-id="1">
                <a href="/analytics/faq">Analytics FAQ</a>
            </div>
        </div>
        <div class="pdf_container">
            <div class="pdf_button">
            <span>DOWNLOAD AS PDF</span>
            <span><i class="fa fa-download"></i></span>
            </div>
        </div>
    </div>

    <script>
        this.sections    = null;
        this.dispatcher = opts.dispatcher;
        this.api        = opts.api;
        var self        = this;

        this.on('mount', function(){
            $("body").on("click",".section",function(){
                $('.section').removeClass('active');
                $(this).addClass('active');
                self.api.getReport();
            });

            $("body").on("click",".pdf_button",function() {
                self.dispatcher.exportToPdf();
            });
        });

        self.api.on(self.api.TEMPLATE_RETRIEVED, function(template)
        {
            self.sections = template.Sections;
            self.update();

        });



    </script>
</survey-report-sections>
