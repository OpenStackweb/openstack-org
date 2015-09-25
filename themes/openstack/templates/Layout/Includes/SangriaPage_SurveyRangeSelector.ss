<form id="range_form" action="{$FormAction}" method="POST">
    <label for="range">$Label</label>
    <select id="range">
        <option <% if $Top.getSurveyRange($FromPage) == "OLD" %>selected<% end_if %> value="OLD">V1</option>
        <option <% if $Top.getSurveyRange($FromPage) == "MARCH_2015" %>selected<% end_if %> value="MARCH_2015">V2 (March 2015)</option>
        <% if $UseSurveyBuilder == "1" %>
        <option <% if $Top.getSurveyRange($FromPage) == "FALL_2015" %>selected<% end_if %> value="FALL_2015">V3 (Fall 2015)</option>
        <% end_if %>
    </select>
    <input type="hidden" id="survey_range" name="survey_range" value="{$Top.getSurveyRange($FromPage)}" />
</form>