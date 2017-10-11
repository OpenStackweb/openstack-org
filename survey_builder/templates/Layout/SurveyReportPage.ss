</div>
<script>
    var reportTemplates = [];
    <% loop $SurveyTemplates.Sort(StartDate,DESC) %>
        reportTemplates.push({
            id: {$ID},
            title: "{$Title.JS}"
        })
    <% end_loop %>
</script>

<div id="survey-analytics-container">
</div>
$ModuleJS('survey-analytics')
$ModuleCSS('survey-analytics')