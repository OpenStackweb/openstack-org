<select id="range" style="width: 300px;">
    <option value="0">--select one --</option>
    <% loop $Top.getEntitySurveyTemplate %>
        <option  <% if $Top.getSurveyRange($FromPage) == $ID %>selected<% end_if %> value="{$ID}">{$NiceName}</option>
    <% end_loop %>
</select>