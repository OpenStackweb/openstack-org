<ul class="nav nav-tabs">
    <% loop $Menu(2) %>
        <li class="$LinkingMode">
            <a href="$Link">About The Summit</a>
        </li>
    <% end_loop %>
    <% loop $Menu(3) %>
        <li class="$LinkingMode">
            <a href="$Link">$MenuTitle</a>
        </li>
    <% end_loop %>
</ul>
