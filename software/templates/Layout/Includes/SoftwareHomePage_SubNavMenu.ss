<div class="container">
    <div class="outer-project-subnav">
        <div class="subpage-slider">
            <ul class="subpage-subnav">
                <% if Top.SubMenuPages %>
                    <% loop Top.SubMenuPages.Sort(Order, ASC) %>
                        <li class="<% if $Top.Active == $Pos %>active<% end_if %>">
                            <a href="{$Url}">{$Label}</a>
                        </li>
                    <% end_loop %>
                <% end_if %>
            </ul>
        </div>
    </div>
</div>
