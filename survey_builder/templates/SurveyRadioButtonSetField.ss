<ul id="$ID" class="$extraClass">
    <% loop $Options %>
        <li class="$Class">
            <div class="radio <% if $isDisabled %> disabled<% end_if %>">
                <label>
                    <input type="radio" name="$Name" id="$ID" value="$Value" <% if $isChecked %> checked<% end_if %>>
                    $Title
                </label>
            </div>
        </li>
    <% end_loop %>
</ul>
