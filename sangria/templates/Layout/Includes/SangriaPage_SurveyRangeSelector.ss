<form id="range_form" action="{$FormAction}" method="POST">
    <label for="range">$Label</label>
    <% if Type == 'SurveyEntity' %>
        <% include SangriaPage_EntitySurveySelector %>
    <% else %>
        <% include SangriaPage_SurveySelector %>
    <% end_if %>
    <input type="hidden" id="survey_range" name="survey_range" value="{$Top.getSurveyRange($FromPage)}" />
</form>