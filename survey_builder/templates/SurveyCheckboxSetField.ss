<% if Groups %>
    <% loop $Groups %>
        <div id="group_{$Slug}">
            <label class="left option-group-label">$Label</label>
            <hr>
            <% include SurveyCheckboxSetField_Layout %>
        </div>
    <% end_loop %>
<% else %>
    <% include SurveyCheckboxSetField_Layout %>
<% end_if %>