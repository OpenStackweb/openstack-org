<h3>OpenStack Online Help</h3>
<ul class="resource-links">
    <% loop $Top.HelpLinks.Sort(SortOrder) %>
        <li>
            <a href="{$Link}">$Label</a>
        </li>
    <% end_loop %>
</ul>