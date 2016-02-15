<select id="range" style="max-width: 125px;">
    <% loop $Top.getSurveys %>
        <option  <% if $Top.getSurveyRange($FromPage) == $ID %>selected<% end_if %> value="{$ID}">{$NiceName}</option>
    <% end_loop %>
</select>