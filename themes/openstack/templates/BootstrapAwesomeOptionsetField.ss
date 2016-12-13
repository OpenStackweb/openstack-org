<% if $Options.Count %>
    <% if $Top.Inline %> <br> <% end_if %>
    <% loop $Options %>
        <div class="radio <% if $Top.Inline %>radio-inline<% end_if %>">
            <input id="$ID" class="radio" name="$Name" type="radio" value="$Value"<% if $isChecked %> checked<% end_if %><% if $isDisabled %> disabled<% end_if %>>
            <label for="$ID" > $Title </label>
        </div>
    <% end_loop %>
<% else %>
    <li>No options available</li>
<% end_if %>
