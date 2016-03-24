</div>
<h1>OpenStack Survey Report</h1>

<div class="container">
    <div class="report_templates_container">
        <select id="report-templates" class="form-control">
            <% loop $SurveyTemplates.Sort(StartDate,ASC) %>
                <option value="{$ID}">{$Title}</option>
            <% end_loop %>
        </select>
    </div>
</div>
<script>



</script>
<survey-report-filters></survey-report-filters>
<div class="container">

    <div class="row">
        <div class="col-md-3">
            <survey-report-sections></survey-report-sections>
        </div>
        <div class="col-md-9" id="dashboard-container">
            <survey-report-dashboard></survey-report-dashboard>
        </div>
    </div>
</div>

<script src="survey_builder/js/report/survey-report-view.bundle.js" type="application/javascript"></script>
