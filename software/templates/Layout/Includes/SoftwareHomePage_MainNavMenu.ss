<!-- Projects Tabs -->
<div class="software-tab-wrapper">
    <div class="container">
        <ul class="nav nav-tabs project-tabs">
            <li <% if Active == 0 %>class="active"<% end_if %>><a href="$Top.Link"><%t Software.OVERVIEW 'Overview' %></a></li>
            <li <% if Active == 1 %>class="active"<% end_if %>><a href="$Top.Link(project-navigator)"><%t Software.PROJECT_NAVIGATOR 'Project Navigator' %></a></li>
            <% if $Top.HasAvailableSampleConfigTypes %>
            <li <% if Active == 2 %>class="active"<% end_if %>><a href="$Top.Link(sample-configs)"><%t Software.SAMPLE_CONFIGURATIONS 'Sample Configurations' %></a></li>
            <% end_if %>
            <% if SubMenuPages %>
                <% loop SubMenuPages.Sort(Order, ASC) %>
                    <li <% if Active == $Top.getCurrentIdx($Pos) %>class="active"<% end_if %>><a href="$Url">$Label</a></li>
                <% end_loop %>
            <% end_if %>
        </ul>
    </div>
</div>
<div class="software-tab-dropdown">
    <div class="dropdown">
        <button aria-expanded="true" aria-haspopup="true" data-toggle="dropdown" id="dropdownMenu1" type="button" class="dropdown-toggle projects-dropdown-btn">
            <%t Software.SELECT_A_PAGE 'Select a Page' %>
            <i class="fa fa-caret-down"></i>
        </button>
        <ul class="dropdown-menu">
            <li <% if Active == 0 %>class="active"<% end_if %>><a href="$Top.Link"><%t Software.OVERVIEW 'Overview' %></a></li>
            <li <% if Active == 1 %>class="active"<% end_if %>><a href="$Top.Link(project-navigator)"><%t Software.PROJECT_NAVIGATOR 'Project Naviagator' %></a></li>
            <% if $Top.HasAvailableSampleConfigTypes %>
                <li <% if Active == 2 %>class="active"<% end_if %>><a href="$Top.Link(sample-configs)"><%t Software.SAMPLE_CONFIGURATIONS 'Sample Configurations' %></a></li>
            <% end_if %>
            <% if SubMenuPages %>
                <% loop SubMenuPages.Sort(Order, ASC) %>
                    <li <% if Active == $Top.getCurrentIdx($Pos) %>class="active"<% end_if %>><a href="$Url">$Label</a></li>
                <% end_loop %>
            <% end_if %>
        </ul>
    </div>
</div>
