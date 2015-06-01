<ul class="nav nav-tabs">
    <li class="{$MainNavClass}">
        <a href="$SummitRoot">About The Summit</a>
    </li>
    <% loop $Menu(3) %>
        <li class="$LinkingMode">
            <a href="$Link">$MenuTitle</a>
        </li>
    <% end_loop %>
</ul>
