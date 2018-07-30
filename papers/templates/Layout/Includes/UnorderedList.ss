<ul>
    <% loop $Top.Items %>
        <li>
            <p>{$_T($Top.Paper.I18nContext, $Content, 1)}</p>
            <% if $SubItems %>
                <% if $SubItemsContainerType == UL %>
                    <% include UnorderedList Paper=$Top.Paper,Items=$SubItems %>
                <% end_if %>
                <% if $SubItemsContainerType == OL %>
                    <% include OrderedList Paper=$Top.Paper,Items=$SubItems %>
                <% end_if %>
            <% end_if %>
        </li>
    <% end_loop %>
</ul>