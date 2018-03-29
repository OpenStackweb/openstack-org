<ul id="$ID" class="{$extraClass} {$OrientationClass}">
    <% loop $Options %>
        <li class="{$Class} {$Top.OrientationClass}">
            <div class="radio <% if $isDisabled %> disabled<% end_if %>">
                <label>
                    <input type="radio" name="$Name" id="$ID" value="$Value" <% if $isChecked %> checked<% end_if %>>
                    $Title.RAW
                </label>
            </div>
        </li>
    <% end_loop %>
</ul>
