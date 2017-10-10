<% if Groups %>
    <% loop $Groups %>
        <div id="group_{$Slug}">
            <label class="left option-group-label">$Label</label>
            <hr>
            <% include SurveyCheckboxSetField_Layout TopID=$Top.ID, extraClass=$Top.extraClass %>
        </div>
    <% end_loop %>
<% else %>
    <% include SurveyCheckboxSetField_Layout TopID=$Top.ID, extraClass=$Top.extraClass %>
<% end_if %>