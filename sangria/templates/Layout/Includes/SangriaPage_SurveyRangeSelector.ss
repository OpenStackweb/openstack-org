<form id="range_form" action="{$FormAction}" method="POST">
    <label for="range">$Label</label>
    <% include SangriaPage_SurveySelector %>
    <input type="hidden" id="survey_range" name="survey_range" value="{$Top.getSurveyRange($FromPage)}" />
</form>