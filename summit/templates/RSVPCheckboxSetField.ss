<% if $Options.Count %>
    <% loop $Options %>
        <div class="checkbox <% if $Up.Inline %>inline<% end_if %>">
            <input id="$ID" class="checkbox" name="$Name" type="checkbox" value="$Value"<% if $isChecked %> checked="checked"<% end_if %><% if $isDisabled %> disabled="disabled"<% end_if %>>
            <label>
                $Title
            </label>
        </div>
    <% end_loop %>
<% else %>
    <div>No options available</div>
<% end_if %>
