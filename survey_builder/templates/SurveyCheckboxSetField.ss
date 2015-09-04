<% if $Options.Count <= 8 %>
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
<% else %>
<table id="$ID" class="$extraClass">
    <% loop $Options %>
        <% if First %>
        <tr>
        <% end_if %>
        <td width="33%">
            <input class="checkbox <% if $Top.isVisible == 0 %>hidden<% end_if %>" id="$ID" <% if $Top.IsRequired%>data-rule-required="true" <% end_if %> name="{$Name}" type="checkbox" value="$Value"<% if $isChecked %> checked="checked"<% end_if %><% if $isDisabled %> disabled="disabled"<% end_if %> />
            <label class="checkbox-label" for="$ID">$Title</label>
        </td>
        <% if not First && not Last && Modulus(3) == 0 %>
        </tr><tr>
        <% end_if %>
        <% if Last %>
        </tr>
        <% end_if %>
    <% end_loop %>
</table>
<% end_if %>