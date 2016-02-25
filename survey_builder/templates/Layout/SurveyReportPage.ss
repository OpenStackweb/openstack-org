</div>
<h1>OpenStack Survey Report</h1>
<script>
    var templates = [];

    <% loop $SurveyTemplates %>
        templates.push({id:{$ID},title:"{$Title.JS}"});
    <% end_loop %>


</script>
<survey-report-filters templates="{ templates }"></survey-report-filters>
<div class="container">

    <div class="row">
        <div class="col-md-4">
            <survey-report-sections></survey-report-sections>
        </div>
        <div class="col-md-8">
            <survey-report-dashboard></survey-report-dashboard>
        </div>
    </div>
</div>

<script src="survey_builder/js/report/survey-report-view.bundle.js" type="application/javascript"></script>
