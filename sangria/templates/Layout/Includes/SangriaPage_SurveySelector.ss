<select id="range" style="max-width: 125px;">
    <option <% if $Top.getSurveyRange($FromPage) == "OLD" %>selected<% end_if %> value="OLD">V1</option>
    <option <% if $Top.getSurveyRange($FromPage) == "MARCH_2015" %>selected<% end_if %> value="MARCH_2015">V2 (March 2015)</option>
    <% loop $Top.getSurveys %>
        <option  <% if $Top.getSurveyRange($FromPage) == $ID %>selected<% end_if %> value="{$ID}">{$NiceName}</option>
    <% end_loop %>
</select>