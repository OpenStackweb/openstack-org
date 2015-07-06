<ul id="$ID" class="$extraClass">
    <% if $Options.Count %>
        <% loop $Options %>
            <li class="$Class">
                <input class="checkbox <% if $Top.isVisible == 0 %>hidden<% end_if %>" id="$ID" <% if $Top.IsRequired%>data-rule-required="true" <% end_if %> name="{$Name}" type="checkbox" value="$Value"<% if $isChecked %> checked="checked"<% end_if %><% if $isDisabled %> disabled="disabled"<% end_if %> />
                <label for="$ID">$Title</label>
            </li>
        <% end_loop %>
    <% else %>
        <li>No options available</li>
    <% end_if %>
</ul>