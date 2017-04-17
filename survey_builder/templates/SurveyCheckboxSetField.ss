<% if $Options.Count <= 10  || $isMobileClient %>
<ul id="$ID" class="$extraClass">
    <% if $Options.Count %>
        <% loop $Options %>
            <li class="{$EvenOdd}">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" class="checkboxset-item" <% if $Top.isVisible == 0 %>hidden<% end_if %>" id="$ID" <% if $Top.IsRequired%>data-rule-required="true" <% end_if %> name="{$Name}" value="$Value"<% if $isChecked %> checked="checked"<% end_if %><% if $isDisabled %> disabled="disabled"<% end_if %> >&nbsp;$Title
                    </label>
                </div>
            </li>
        <% end_loop %>
    <% else %>
        <li>$_T("survey_ui","No options available")</li>
    <% end_if %>
</ul>
<% else %>
<table id="$ID" class="$extraClass">
    <tr>
    <% loop $Options %>
        <% if First %>
        <td width="33%"><ul>
        <% end_if %>
        <li class="{$EvenOdd}">
            <div class="checkbox">
                <label>
                    <input type="checkbox" class="checkboxset-item" <% if $Top.isVisible == 0 %>hidden<% end_if %>" id="$ID" <% if $Top.IsRequired%>data-rule-required="true" <% end_if %> name="{$Name}" value="$Value"<% if $isChecked %> checked="checked"<% end_if %><% if $isDisabled %> disabled="disabled"<% end_if %> >&nbsp;$Title
                </label>
            </div>
        </li>
        <% if not First && not Last && IsThirdPart %>
        </ul></td><td width="33%"><ul>
        <% end_if %>
        <% if Last %>
        </ul></td>
        <% end_if %>
    <% end_loop %>
    </tr>
</table>
<% end_if %>