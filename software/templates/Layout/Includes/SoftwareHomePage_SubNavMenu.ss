<div class="container">
    <div class="outer-project-subnav">
        <div class="sample-configs-slider">
            <ul class="sample-configs-subnav">
                <% if Top.SubMenuPages %>
                    <% loop Top.SubMenuPages.Sort(Order, ASC) %>
                        <li class="sample_config_type <% if $Top.Active == $Pos %>active<% end_if %>">
                            <a href="{$Url}">{$Label}</a>
                        </li>
                    <% end_loop %>
                <% end_if %>
            </ul>
        </div>
    </div>
</div>
