<% if $Options.Count <= 10 %>
<ul id="$ID" class="$extraClass">
    <% if $Options.Count %>
        <% loop $Options %>
            <li class="{$EvenOdd}">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" <% if $Top.isVisible == 0 %>hidden<% end_if %>" id="$ID" <% if $Top.IsRequired%>data-rule-required="true" <% end_if %> name="{$Name}" value="$Value"<% if $isChecked %> checked="checked"<% end_if %><% if $isDisabled %> disabled="disabled"<% end_if %> >&nbsp;$Title
                    </label>
                </div>
            </li>
        <% end_loop %>
    <% else %>
        <li>No options available</li>
    <% end_if %>
</ul>
<% else %>
<table id="$ID" class="$extraClass">
    <tr>
    <% loop $Options %>
        <% if First %>
        <td><ul>
        <% end_if %>
        <li class="{$EvenOdd}">
            <div class="checkbox">
                <label>
                    <input type="checkbox" <% if $Top.isVisible == 0 %>hidden<% end_if %>" id="$ID" <% if $Top.IsRequired%>data-rule-required="true" <% end_if %> name="{$Name}" value="$Value"<% if $isChecked %> checked="checked"<% end_if %><% if $isDisabled %> disabled="disabled"<% end_if %> >&nbsp;$Title
                </label>
            </div>
        </li>
        <% if not First && not Last && Modulus(6) == 0 %>
        </ul></td><td><ul>
        <% end_if %>
        <% if Last %>
        </ul></td>
        <% end_if %>
    <% end_loop %>
    </tr>
</table>
<% end_if %>